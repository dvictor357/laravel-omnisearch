<?php

namespace OmniSearch;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
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
        $this->registerPublishing();
        $this->registerViews();
        $this->registerLivewireComponents();
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

            // Full publish
            $this->publishes([
                __DIR__ . '/../config/omnisearch.php' => config_path('omnisearch.php'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/omnisearch'),
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
}
