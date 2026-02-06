<?php

namespace OmniSearch\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'omnisearch:install';

    protected $description = 'Install OmniSearch package assets and configuration';

    public function handle(): int
    {
        $this->info('Installing OmniSearch...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'omnisearch-config',
            '--force' => true,
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--tag' => 'omnisearch-views',
            '--force' => true,
        ]);

        $this->info('OmniSearch installed successfully!');
        $this->info('You can now use <comment><livewire:omnisearch /></comment> in your layouts.');

        return Command::SUCCESS;
    }
}
