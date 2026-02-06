<?php

namespace OmniSearch\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultSelected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $id,
        public string $title,
        public string $actionType,
        public ?string $url = null,
        public ?string $source = null,
    ) {}
}
