<?php

namespace OmniSearch\Sources;

use Illuminate\Support\Collection;
use OmniSearch\Contracts\SearchSource;
use OmniSearch\Data\Result;

abstract class CommandSource implements SearchSource
{
    protected array $dependencyResults = [];

    public function getDependencies(): array
    {
        return [];
    }

    public function getSynonyms(): array
    {
        return [];
    }

    /**
     * Resolve dependencies before searching.
     */
    public function resolveDependencies(array $dependencies): void
    {
        foreach ($dependencies as $key => $dependency) {
            if (is_string($dependency) && class_exists($dependency)) {
                $this->dependencyResults[$key] = app($dependency);
            }
        }
    }

    /**
     * Get a resolved dependency.
     */
    protected function getDependency(string $key): ?object
    {
        return $this->dependencyResults[$key] ?? null;
    }

    /**
     * Execute the command with resolved dependencies.
     */
    abstract public function execute(...$args): mixed;
}
