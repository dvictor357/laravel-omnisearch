<?php

namespace OmniSearch\Facades;

use Illuminate\Support\Facades\Facade;
use OmniSearch\Services\SearchManager;

/**
 * @method static \Illuminate\Support\Collection search(string $query)
 * @method static \Illuminate\Support\Collection searchGrouped(string $query)
 * @method static \Illuminate\Support\Collection getSources()
 * @method static \OmniSearch\Services\SearchManager registerSource(\OmniSearch\Contracts\SearchSource $source)
 *
 * @see \OmniSearch\Services\SearchManager
 */
class OmniSearch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SearchManager::class;
    }
}
