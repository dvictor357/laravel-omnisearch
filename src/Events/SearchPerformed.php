<?php

namespace OmniSearch\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchPerformed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $query,
        public int $resultsCount,
        public float $duration,
        public array $sources = [],
    ) {}
}
