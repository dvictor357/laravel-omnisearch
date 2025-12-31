<?php

namespace OmniSearch\Sources;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use OmniSearch\Contracts\SearchSource;
use OmniSearch\Data\Result;

class RouteSource implements SearchSource
{
    public function getKey(): string
    {
        return 'routes';
    }

    public function getLabel(): string
    {
        return 'Pages';
    }

    public function getIcon(): string
    {
        return 'link';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function search(string $query): Collection
    {
        $includePatterns = config('omnisearch.routes.include', ['*']);
        $excludePatterns = config('omnisearch.routes.exclude', []);
        $limit = config('omnisearch.ui.max_results', 10);

        $query = strtolower($query);

        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods()))
            ->filter(fn ($route) => $route->getName() !== null)
            ->filter(fn ($route) => $this->matchesPatterns($route->getName(), $includePatterns))
            ->reject(fn ($route) => $this->matchesPatterns($route->getName(), $excludePatterns))
            ->filter(function ($route) use ($query) {
                $name = strtolower($route->getName());
                $uri = strtolower($route->uri());

                return str_contains($name, $query) || str_contains($uri, $query);
            })
            ->take($limit)
            ->map(function ($route) {
                $name = $route->getName();
                $uri = $route->uri();

                // Skip routes with required parameters
                if (preg_match('/\{[^?}]+\}/', $uri)) {
                    return null;
                }

                return Result::navigate(
                    id: "route:{$name}",
                    title: $this->formatRouteName($name),
                    description: "/{$uri}",
                    url: url($uri),
                    icon: $this->getIcon(),
                    group: $this->getLabel(),
                );
            })
            ->filter()
            ->values();
    }

    protected function matchesPatterns(string $value, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (fnmatch($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    protected function formatRouteName(string $name): string
    {
        // Convert 'users.index' to 'Users Index'
        return collect(explode('.', $name))
            ->map(fn ($part) => ucfirst($part))
            ->implode(' › ');
    }
}
