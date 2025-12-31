<?php

namespace OmniSearch\Data;

use OmniSearch\Contracts\SearchResult;

class Result implements SearchResult
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected ?string $description,
        protected ?string $url,
        protected string $icon,
        protected string $group,
        protected string $actionType = 'navigate',
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'icon' => $this->icon,
            'group' => $this->group,
            'actionType' => $this->actionType,
        ];
    }

    /**
     * Create a navigation result.
     */
    public static function navigate(
        string $id,
        string $title,
        ?string $description,
        string $url,
        string $icon,
        string $group,
    ): static {
        return new static($id, $title, $description, $url, $icon, $group, 'navigate');
    }

    /**
     * Create an action result (triggers a Livewire action).
     */
    public static function action(
        string $id,
        string $title,
        ?string $description,
        string $icon,
        string $group,
    ): static {
        return new static($id, $title, $description, null, $icon, $group, 'action');
    }
}
