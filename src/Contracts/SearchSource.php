<?php

namespace OmniSearch\Contracts;

use Illuminate\Support\Collection;

interface SearchSource
{
    /**
     * Get the unique identifier for this source.
     */
    public function getKey(): string;

    /**
     * Get the display label for this source group.
     */
    public function getLabel(): string;

    /**
     * Search for results matching the given query.
     *
     * @return Collection<int, SearchResult>
     */
    public function search(string $query): Collection;

    /**
     * Determine if the current user can access this source.
     */
    public function authorize(): bool;

    /**
     * Get the icon identifier for this source.
     */
    public function getIcon(): string;

    /**
     * Get synonyms/aliases for search terms.
     *
     * @return array<string, string>
     */
    public function getSynonyms(): array;

    /**
     * Get dependencies for this source (used by CommandSource).
     *
     * @return array<string, class-string>
     */
    public function getDependencies(): array;
}
