<?php

namespace OmniSearch\Sources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use OmniSearch\Contracts\SearchSource;
use OmniSearch\Data\Result;

class ModelSource implements SearchSource
{
    public function getKey(): string
    {
        return 'models';
    }

    public function getLabel(): string
    {
        return 'Results';
    }

    public function getIcon(): string
    {
        return 'database';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function search(string $query): Collection
    {
        $modelsConfig = config('omnisearch.models', []);
        $results = collect();

        foreach ($modelsConfig as $modelClass => $config) {
            if (! class_exists($modelClass)) {
                continue;
            }

            $modelResults = $this->searchModel($modelClass, $config, $query);
            $results = $results->merge($modelResults);
        }

        return $results->take(config('omnisearch.ui.max_results', 10));
    }

    protected function searchModel(string $modelClass, array $config, string $query): Collection
    {
        $columns = $config['columns'] ?? [];
        $titleColumn = $config['title'] ?? $columns[0] ?? 'id';
        $descriptionColumn = $config['description'] ?? null;
        $route = $config['route'] ?? null;
        $icon = $config['icon'] ?? 'file';
        $limit = $config['limit'] ?? 5;
        $group = $config['group'] ?? class_basename($modelClass);

        if (empty($columns)) {
            return collect();
        }

        /** @var Model $modelInstance */
        $modelInstance = new $modelClass();

        $results = $modelInstance->newQuery()
            ->where(function ($q) use ($columns, $query) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$query}%");
                }
            })
            ->take($limit)
            ->get();

        return $results->map(function (Model $model) use ($titleColumn, $descriptionColumn, $route, $icon, $group) {
            $url = null;

            if ($route) {
                try {
                    $url = route($route, $model->getKey());
                } catch (\Exception) {
                    $url = null;
                }
            }

            return Result::navigate(
                id: "model:{$model->getMorphClass()}:{$model->getKey()}",
                title: (string) $model->getAttribute($titleColumn),
                description: $descriptionColumn ? (string) $model->getAttribute($descriptionColumn) : null,
                url: $url,
                icon: $icon,
                group: $group,
            );
        });
    }
}
