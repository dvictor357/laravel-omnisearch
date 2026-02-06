<?php

namespace OmniSearch\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeSourceCommand extends Command
{
    protected $signature = 'omnisearch:make-source {name : The name of the source class}';

    protected $description = 'Create a new OmniSearch source class';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = ucfirst($name).'Source';

        $path = app_path("OmniSearch/{$className}.php");

        if (File::exists($path)) {
            $this->error("Source {$className} already exists!");

            return Command::FAILURE;
        }

        $content = $this->getStub($name);

        File::ensureDirectoryExists(app_path('OmniSearch'));
        File::put($path, $content);

        $this->info("Source {$className} created successfully!");
        $this->info("Don't forget to register it in config/omnisearch.php");

        return Command::SUCCESS;
    }

    protected function getStub(string $name): string
    {
        return <<<PHP
<?php

namespace App\OmniSearch;

use Illuminate\Support\Collection;
use OmniSearch\Contracts\SearchSource;
use OmniSearch\Data\Result;

class {$this->getClassName($name)}Source implements SearchSource
{
    public function getKey(): string
    {
        return '{$name}';
    }

    public function getLabel(): string
    {
        return ucfirst($name);
    }

    public function getIcon(): string
    {
        return 'database';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getSynonyms(): array
    {
        return [];
    }

    public function getDependencies(): array
    {
        return [];
    }

    public function search(string \$query): Collection
    {
        // Implement your search logic here
        return collect();
    }
}
PHP;
    }

    protected function getClassName(string $name): string
    {
        return ucfirst($name);
    }
}
