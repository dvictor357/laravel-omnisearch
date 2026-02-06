<?php

namespace OmniSearch\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use OmniSearch\Contracts\SearchSource;
use OmniSearch\Contracts\SearchResult;

class SearchManager
{
    /**
     * Resolved source instances.
     *
     * @var array<string, SearchSource>
     */
    protected array $sources = [];

    public function __construct(
        protected Application $app,
    ) {}

    /**
     * Search across all registered sources.
     *
     * @return Collection<int, array>
     */
    public function search(string $query): Collection
    {
        if (trim($query) === '') {
            return collect();
        }

        return $this->getSources()
            ->filter(fn (SearchSource $source) => $source->authorize())
            ->flatMap(fn (SearchSource $source) => $source->search($query))
            ->sortByDesc(fn (SearchResult $result) => $result->getScore() ?? 1)
            ->map(fn ($result) => $result->toArray())
            ->values();
    }

    /**
     * Search with relevance scoring.
     *
     * @return Collection<int, array>
     */
    public function searchWithScoring(string $query): Collection
    {
        if (trim($query) === '') {
            return collect();
        }

        $results = $this->getSources()
            ->filter(fn (SearchSource $source) => $source->authorize())
            ->flatMap(fn (SearchSource $source) => $source->search($query));

        // Apply relevance scoring
        $results = $this->applyRelevanceScoring($results, $query);

        return $results
            ->sortByDesc('score')
            ->values();
    }

    /**
     * Apply relevance scoring to results.
     */
    protected function applyRelevanceScoring(Collection $results, string $query): Collection
    {
        $query = strtolower(trim($query));
        $queryTerms = array_filter(explode(' ', $query));

        return $results->map(function (SearchResult $result) use ($query, $queryTerms) {
            $title = strtolower($result->getTitle());
            $description = strtolower($result->getDescription() ?? '');
            $score = $result->getScore() ?? 0;

            // Exact match in title (highest boost)
            if ($title === $query) {
                $score += 100;
            }
            // Title starts with query
            elseif (str_starts_with($title, $query)) {
                $score += 50;
            }
            // Title contains query
            elseif (str_contains($title, $query)) {
                $score += 25;
            }

            // Check each query term
            foreach ($queryTerms as $term) {
                if (str_contains($title, $term)) {
                    $score += 10;
                }
                if (str_contains($description, $term)) {
                    $score += 5;
                }
            }

            // Boost if description matches query exactly
            if ($description === $query) {
                $score += 20;
            }

            return $result->toArray() + ['score' => $score];
        });
    }

    /**
     * Get all registered and resolved sources.
     *
     * @return Collection<int, SearchSource>
     */
    public function getSources(): Collection
    {
        $sourceClasses = config('omnisearch.sources', []);

        foreach ($sourceClasses as $sourceClass) {
            if (! isset($this->sources[$sourceClass])) {
                $this->sources[$sourceClass] = $this->app->make($sourceClass);
            }
        }

        return collect($this->sources);
    }

    /**
     * Register a custom source at runtime.
     */
    public function registerSource(SearchSource $source): static
    {
        $this->sources[$source::class] = $source;

        return $this;
    }

    /**
     * Get grouped results for display.
     *
     * @return Collection<string, Collection>
     */
    public function searchGrouped(string $query): Collection
    {
        return $this->search($query)->groupBy('group');
    }

    /**
     * Get grouped results with scoring.
     *
     * @return Collection<string, Collection>
     */
    public function searchGroupedWithScoring(string $query): Collection
    {
        return $this->searchWithScoring($query)->groupBy('group');
    }
}
