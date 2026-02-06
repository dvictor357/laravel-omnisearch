<?php

namespace OmniSearch\Console;

use Illuminate\Console\Command;

class ClearCacheCommand extends Command
{
    protected $signature = 'omnisearch:clear-cache';

    protected $description = 'Clear OmniSearch cached data';

    public function handle(): int
    {
        $this->info('Clearing OmniSearch cache...');

        // Clear route cache if applicable
        $this->call('route:clear');

        // Clear config cache
        $this->call('config:clear');

        $this->info('OmniSearch cache cleared successfully!');

        return Command::SUCCESS;
    }
}
