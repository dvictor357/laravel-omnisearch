<?php

namespace OmniSearch;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use OmniSearch\Console\ClearCacheCommand;
use OmniSearch\Console\InstallCommand;
use OmniSearch\Console\MakeSourceCommand;
use OmniSearch\Http\Livewire\SearchModal;
use OmniSearch\Services\SearchManager;

class OmniSearchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/omnisearch.php', 'omnisearch');

        $this->app->singleton(SearchManager::class, function ($app) {
            return new SearchManager($app);
        });

        $this->app->alias(SearchManager::class, 'omnisearch');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerPublishing();
        $this->registerViews();
        $this->registerLivewireComponents();
        $this->registerCommands();
    }

    /**
     * Register the package translations.
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'omnisearch');
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../config/omnisearch.php' => config_path('omnisearch.php'),
            ], 'omnisearch-config');

            // Views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/omnisearch'),
            ], 'omnisearch-views');

            // Translations
            $this->publishes([
                __DIR__ . '/../lang' => lang_path('vendor/omnisearch'),
            ], 'omnisearch-lang');

            // Full publish
            $this->publishes([
                __DIR__ . '/../config/omnisearch.php' => config_path('omnisearch.php'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/omnisearch'),
                __DIR__ . '/../lang' => lang_path('vendor/omnisearch'),
            ], 'omnisearch');
        }
    }

    /**
     * Register the package views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'omnisearch');
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('omnisearch', SearchModal::class);
    }

    /**
     * Register console commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeSourceCommand::class,
                ClearCacheCommand::class,
            ]);
        }
    }
}
