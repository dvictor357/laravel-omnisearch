<?php

namespace OmniSearch\Http\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use OmniSearch\Facades\OmniSearch;

class SearchModal extends Component
{
    public string $query = '';

    public array $results = [];

    public array $groupedResults = [];

    public array $recentSearches = [];

    public int $selectedIndex = 0;

    public bool $isOpen = false;

    public bool $isLoading = false;

    public function mount(): void
    {
        $this->recentSearches = $this->getRecentSearches();
    }

    public function updatedQuery(): void
    {
        $this->search();
    }

    public function search(): void
    {
        if (trim($this->query) === '') {
            $this->results = [];
            $this->groupedResults = [];
            $this->selectedIndex = 0;

            return;
        }

        $this->isLoading = true;

        $this->results = OmniSearch::search($this->query)->toArray();
        $this->groupedResults = OmniSearch::searchGrouped($this->query)->toArray();
        $this->selectedIndex = 0;

        $this->isLoading = false;
    }

    #[On('omnisearch:open')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->query = '';
        $this->results = [];
        $this->groupedResults = [];
        $this->recentSearches = $this->getRecentSearches();
        $this->selectedIndex = 0;
    }

    #[On('omnisearch:close')]
    public function close(): void
    {
        $this->isOpen = false;
    }

    #[On('omnisearch:toggle')]
    public function toggle(): void
    {
        $this->isOpen ? $this->close() : $this->open();
    }

    public function selectNext(): void
    {
        $totalResults = count($this->results);
        if ($totalResults === 0) {
            return;
        }

        $this->selectedIndex = ($this->selectedIndex + 1) % $totalResults;
    }

    public function selectPrevious(): void
    {
        $totalResults = count($this->results);
        if ($totalResults === 0) {
            return;
        }

        $this->selectedIndex = $this->selectedIndex > 0
            ? $this->selectedIndex - 1
            : $totalResults - 1;
    }

    public function selectResult(): void
    {
        $result = $this->results[$this->selectedIndex] ?? null;

        if (!$result) {
            return;
        }

        // Save to recent searches
        $this->addToRecentSearches($result);

        // Handle based on action type
        match ($result['actionType'] ?? 'navigate') {
            'navigate' => $this->dispatch('omnisearch:navigate', url: $result['url']),
            'copy' => $this->dispatch('omnisearch:copy', text: $result['payload']),
            'modal' => $this->dispatch('omnisearch:open-modal', name: $result['payload']),
            default => $this->dispatch('omnisearch:navigate', url: $result['url']),
        };

        $this->close();
    }

    public function selectRecent(array $result): void
    {
        $this->addToRecentSearches($result);

        match ($result['actionType'] ?? 'navigate') {
            'navigate' => $this->dispatch('omnisearch:navigate', url: $result['url']),
            'copy' => $this->dispatch('omnisearch:copy', text: $result['payload']),
            'modal' => $this->dispatch('omnisearch:open-modal', name: $result['payload']),
            default => $this->dispatch('omnisearch:navigate', url: $result['url']),
        };

        $this->close();
    }

    public function clearRecentSearches(): void
    {
        $this->recentSearches = [];
        $this->dispatch('omnisearch:clear-recent');
    }

    public function navigateTo(string $url): void
    {
        $this->dispatch('omnisearch:navigate', url: $url);
        $this->close();
    }

    protected function getRecentSearches(): array
    {
        // This will be populated from frontend via JavaScript
        return [];
    }

    protected function addToRecentSearches(array $result): void
    {
        // This will be called from frontend via JavaScript
        $this->dispatch('omnisearch:add-recent', result: $result);
    }

    public function render()
    {
        return view('omnisearch::search-modal');
    }
}
