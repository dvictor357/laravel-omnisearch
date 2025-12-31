<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keyboard Shortcut
    |--------------------------------------------------------------------------
    |
    | The keyboard shortcut to open the OmniSearch modal. This uses the
    | standard modifier + key format. The modifier will automatically
    | adapt (Cmd on Mac, Ctrl on Windows/Linux).
    |
    */

    'shortcut' => 'k',

    /*
    |--------------------------------------------------------------------------
    | Search Sources
    |--------------------------------------------------------------------------
    |
    | Define which search sources are enabled. Each source class must
    | implement the OmniSearch\Contracts\SearchSource interface.
    |
    */

    'sources' => [
        OmniSearch\Sources\ModelSource::class,
        OmniSearch\Sources\RouteSource::class,
        // OmniSearch\Sources\CommandSource::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Searchable Models
    |--------------------------------------------------------------------------
    |
    | Define models that should be searchable via OmniSearch. Each entry
    | maps a model class to its searchable columns and display settings.
    |
    */

    'models' => [
        // App\Models\User::class => [
        //     'columns' => ['name', 'email'],
        //     'title' => 'name',
        //     'description' => 'email',
        //     'route' => 'users.show', // Named route, will receive model ID
        //     'icon' => 'user',
        //     'limit' => 5,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Filtering
    |--------------------------------------------------------------------------
    |
    | Configure which routes should appear in OmniSearch. You can include
    | or exclude routes by name patterns.
    |
    */

    'routes' => [
        'include' => ['*'],
        'exclude' => ['api.*', 'sanctum.*', 'livewire.*', 'ignition.*'],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Customize the appearance and behavior of the search modal.
    |
    */

    'ui' => [
        'placeholder' => 'Search anything...',
        'debounce' => 300, // milliseconds
        'max_results' => 10,
        'show_keyboard_hints' => true,
    ],

];
