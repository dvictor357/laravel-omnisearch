<?php

namespace OmniSearch\Data;

use OmniSearch\Contracts\SearchResult;

class Result implements SearchResult
{
    // Action type constants
    public const ACTION_NAVIGATE = 'navigate';
    public const ACTION_COPY = 'copy';
    public const ACTION_MODAL = 'modal';

    public function __construct(
        protected string $id,
        protected string $title,
        protected ?string $description,
        protected ?string $url,
        protected string $icon,
        protected string $group,
        protected string $actionType = self::ACTION_NAVIGATE,
        protected ?float $score = null,
        protected ?string $actionPayload = null,
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

    /**
     * Get the relevance score for sorting (higher is better).
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * Get additional payload for action types.
     */
    public function getActionPayload(): ?string
    {
        return $this->actionPayload;
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
            'score' => $this->score,
            'payload' => $this->actionPayload,
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
        return new static($id, $title, $description, $url, $icon, $group, self::ACTION_NAVIGATE);
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
        ?string $payload = null,
    ): static {
        return new static($id, $title, $description, null, $icon, $group, self::ACTION_COPY, null, $payload);
    }

    /**
     * Create a copy result (copies text to clipboard).
     */
    public static function copy(
        string $id,
        string $title,
        ?string $description,
        string $textToCopy,
        string $icon,
        string $group,
    ): static {
        return new static($id, $title, $description, null, $icon, $group, self::ACTION_COPY, null, $textToCopy);
    }

    /**
     * Create a modal result (opens a modal).
     */
    public static function modal(
        string $id,
        string $title,
        ?string $description,
        string $modalName,
        string $icon,
        string $group,
    ): static {
        return new static($id, $title, $description, null, $icon, $group, self::ACTION_MODAL, null, $modalName);
    }

    /**
     * Create a result with a relevance score.
     */
    public static function scored(
        string $id,
        string $title,
        ?string $description,
        ?string $url,
        string $icon,
        string $group,
        string $actionType,
        float $score,
        ?string $payload = null,
    ): static {
        return new static($id, $title, $description, $url, $icon, $group, $actionType, $score, $payload);
    }
}
