<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keyboard Shortcuts
    |--------------------------------------------------------------------------
    |
    | Configure the keyboard shortcuts to open the OmniSearch modal.
    | You can specify multiple shortcuts and the modifier key.
    | The modifier will automatically adapt (Cmd on Mac, Ctrl on Windows/Linux).
    |
    */

    'shortcuts' => ['k', '/'],

    'modifier' => 'cmd', // 'cmd', 'ctrl', or 'alt'

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
        //     'group' => 'Users', // Optional custom group label
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
        'max_recent_searches' => 10,
        'enable_history' => true,

        /*
        |--------------------------------------------------------------------------
        | Theme Configuration
        |--------------------------------------------------------------------------
        |
        | Customize the appearance with CSS variables.
        |
        */

        'theme' => [
            'primary' => '#8b5cf6',
            'bg' => 'rgba(30, 30, 46, 0.85)',
            'radius' => '16px',
            'accent' => 'rgba(139, 92, 246, 0.3)',
            'text' => '#ffffff',
            'text_muted' => 'rgba(255, 255, 255, 0.5)',
            'border' => 'rgba(255, 255, 255, 0.08)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    |
    | Configure search behavior and relevance scoring.
    |
    */

    'search' => [
        'use_scoring' => true,
        'min_score' => 0,
        'highlight_matches' => true,
    ],

];
