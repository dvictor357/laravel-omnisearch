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

    public int $selectedIndex = 0;

    public bool $isOpen = false;

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

        $this->results = OmniSearch::search($this->query)->toArray();
        $this->groupedResults = OmniSearch::searchGrouped($this->query)->toArray();
        $this->selectedIndex = 0;
    }

    #[On('omnisearch:open')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->query = '';
        $this->results = [];
        $this->groupedResults = [];
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
        if ($this->selectedIndex < count($this->results) - 1) {
            $this->selectedIndex++;
        }
    }

    public function selectPrevious(): void
    {
        if ($this->selectedIndex > 0) {
            $this->selectedIndex--;
        }
    }

    public function selectResult(): void
    {
        if (empty($this->results)) {
            return;
        }

        $result = $this->results[$this->selectedIndex] ?? null;

        if ($result && $result['url']) {
            $this->dispatch('omnisearch:navigate', url: $result['url']);
        }

        $this->close();
    }

    public function navigateTo(string $url): void
    {
        $this->dispatch('omnisearch:navigate', url: $url);
        $this->close();
    }

    public function render()
    {
        return view('omnisearch::search-modal');
    }
}
