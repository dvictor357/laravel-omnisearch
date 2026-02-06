<?php

namespace OmniSearch\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModalOpened
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ?string $trigger = null,
    ) {}
}
