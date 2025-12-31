<?php

namespace OmniSearch\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use OmniSearch\Contracts\SearchSource;

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
            ->map(fn ($result) => $result->toArray())
            ->values();
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
}
