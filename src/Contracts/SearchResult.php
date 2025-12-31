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
     * Get the action type: 'navigate', 'action', or 'copy'.
     */
    public function getActionType(): string;

    /**
     * Convert to array for JSON serialization.
     */
    public function toArray(): array;
}
