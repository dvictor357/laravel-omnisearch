<?php

namespace OmniSearch\Contracts;

interface SearchResult
{
    /**
     * Get the unique identifier for this result.
     */
    public function getId(): string;

    /**
     * Get the display title.
     */
    public function getTitle(): string;

    /**
     * Get the display description/subtitle.
     */
    public function getDescription(): ?string;

    /**
     * Get the URL to navigate to when selected.
     */
    public function getUrl(): ?string;

    /**
     * Get the icon identifier.
     */
    public function getIcon(): string;

    /**
     * Get the source group this result belongs to.
     */
    public function getGroup(): string;

    /**
     * Get the action type: 'navigate', 'copy', or 'modal'.
     */
    public function getActionType(): string;

    /**
     * Get the relevance score for sorting (higher is better).
     */
    public function getScore(): ?float;

    /**
     * Get additional payload for action types (text to copy, modal name, etc.).
     */
    public function getActionPayload(): ?string;

    /**
     * Convert to array for JSON serialization.
     */
    public function toArray(): array;
}
