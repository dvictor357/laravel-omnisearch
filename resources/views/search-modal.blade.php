<div
    x-data="omnisearch()"
    x-init="init()"
    :style="themeStyles"
    x-on:omnisearch:navigate.window="window.location.href = $event.detail.url"
    x-on:omnisearch:copy.window="navigator.clipboard.writeText($event.detail.text); showCopyToast()"
    x-on:omnisearch:open-modal.window="$wire.dispatch('open-modal', { name: $event.detail.name })"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="omnisearch-overlay"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('omnisearch::messages.aria_label') }}"
>
    {{-- Backdrop --}}
    <div
        class="omnisearch-backdrop"
        x-on:click="close()"
        aria-hidden="true"
    ></div>

    {{-- Modal --}}
    <div
        class="omnisearch-modal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
    >
        {{-- Search Input --}}
        <div class="omnisearch-input-container">
            <div class="omnisearch-search-icon" aria-hidden="true">
                <x-omnisearch::icon name="search" />
            </div>
            <input
                x-ref="searchInput"
                type="text"
                wire:model.live.debounce.{{ config('omnisearch.ui.debounce', 300) }}ms="query"
                placeholder="{{ config('omnisearch.ui.placeholder', 'Search anything...') }}"
                class="omnisearch-input"
                x-on:keydown="handleKeydown"
                x-on:input="recentVisible = query === ''"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
                aria-label="{{ __('omnisearch::messages.search_input_placeholder') }}"
            />
            @if(config('omnisearch.ui.show_keyboard_hints', true))
                <div class="omnisearch-shortcut-hint" x-show="!query">
                    <kbd x-text="shortcutDisplay"></kbd>
                </div>
            @endif
        </div>

        {{-- Loading State --}}
        <div x-show="isLoading" class="omnisearch-loading" x-transition>
            <div class="omnisearch-spinner"></div>
        </div>

        {{-- Recent Searches --}}
        <div
            x-show="recentVisible && recentSearches.length > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="omnisearch-recent"
        >
            <div class="omnisearch-recent-header">
                <span class="omnisearch-recent-label">{{ __('omnisearch::messages.recent') }}</span>
                <button
                    type="button"
                    class="omnisearch-clear-btn"
                    x-on:click="clearRecentSearches()"
                >
                    {{ __('omnisearch::messages.clear') }}
                </button>
            </div>
            <template x-for="(result, index) in recentSearches" :key="'recent-' + result.id">
                <button
                    type="button"
                    class="omnisearch-result"
                    :class="{ 'omnisearch-result--selected': recentSelectedIndex === index }"
                    x-on:click="selectRecent(result)"
                    x-on:mouseenter="recentSelectedIndex = index"
                >
                    <div class="omnisearch-result-icon">
                        <x-omnisearch::icon :name="result.icon" />
                    </div>
                    <div class="omnisearch-result-content">
                        <div class="omnisearch-result-title" x-text="result.title"></div>
                        <div class="omnisearch-result-description" x-text="result.description" x-show="result.description"></div>
                    </div>
                    <div class="omnisearch-result-action">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </button>
            </template>
        </div>

        {{-- Results --}}
        <div
            x-show="!recentVisible || recentSearches.length === 0"
            x-transition
        >
            @if(!empty($groupedResults))
                <div class="omnisearch-results" role="listbox">
                    @php $flatIndex = 0; @endphp
                    @foreach($groupedResults as $group => $items)
                        <div class="omnisearch-group" role="group" aria-label="{{ $group }}">
                            <div class="omnisearch-group-label">{{ $group }}</div>
                            @foreach($items as $result)
                                <button
                                    type="button"
                                    class="omnisearch-result"
                                    :class="{ 'omnisearch-result--selected': selectedIndex === {{ $flatIndex }} }"
                                    wire:click="selectResult()"
                                    x-on:mouseenter="selectedIndex = {{ $flatIndex }}"
                                    role="option"
                                    :aria-selected="selectedIndex === {{ $flatIndex }}"
                                >
                                    <div class="omnisearch-result-icon">
                                        <x-omnisearch::icon name="{{ $result['icon'] }}" />
                                    </div>
                                    <div class="omnisearch-result-content">
                                        <div class="omnisearch-result-title">{{ $result['title'] }}</div>
                                        @if($result['description'])
                                            <div class="omnisearch-result-description">{{ $result['description'] }}</div>
                                        @endif
                                    </div>
                                    <div class="omnisearch-result-action">
                                        @if(($result['actionType'] ?? 'navigate') === 'copy')
                                            <x-omnisearch::icon name="copy" />
                                        @elseif(($result['actionType'] ?? 'navigate') === 'modal')
                                            <x-omnisearch::icon name="expand" />
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                                @php $flatIndex++; @endphp
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @elseif($query !== '')
                <div class="omnisearch-empty">
                    <x-omnisearch::icon name="search" class="omnisearch-empty-icon" />
                    <p>{{ __('omnisearch::messages.no_results') }} <strong x-text="query"></strong></p>
                </div>
            @else
                <div class="omnisearch-empty">
                    <x-omnisearch::icon name="sparkles" class="omnisearch-empty-icon" />
                    <p>{{ __('omnisearch::messages.start_typing') }}</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        @if(config('omnisearch.ui.show_keyboard_hints', true))
            <div class="omnisearch-footer">
                <div class="omnisearch-footer-hints">
                    <span><kbd>&#8593;</kbd><kbd>&#8595;</kbd> {{ __('omnisearch::messages.navigate') }}</span>
                    <span><kbd>enter</kbd> {{ __('omnisearch::messages.select') }}</span>
                    <span><kbd>esc</kbd> {{ __('omnisearch::messages.close') }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Copy Toast --}}
    <div
        x-show="showCopyToastVisible"
        x-transition
        class="omnisearch-toast"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
    >
        <x-omnisearch::icon name="check" />
        <span>{{ __('omnisearch::messages.copied') }}</span>
    </div>

    <style>
        [x-cloak] { display: none !important; }

        /* CSS Variables for Theming */
        .omnisearch-overlay {
            --omnisearch-primary: {{ config('omnisearch.ui.theme.primary', '#8b5cf6') }};
            --omnisearch-bg: {{ config('omnisearch.ui.theme.bg', 'rgba(30, 30, 46, 0.85)') }};
            --omnisearch-radius: {{ config('omnisearch.ui.theme.radius', '16px') }};
            --omnisearch-accent: {{ config('omnisearch.ui.theme.accent', 'rgba(139, 92, 246, 0.3)') }};
            --omnisearch-text: #ffffff;
            --omnisearch-text-muted: rgba(255, 255, 255, 0.5);
            --omnisearch-border: rgba(255, 255, 255, 0.08);
        }

        .omnisearch-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 15vh;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .omnisearch-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }

        .omnisearch-modal {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 16px;
            background: var(--omnisearch-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--omnisearch-accent);
            border-radius: var(--omnisearch-radius);
            box-shadow:
                0 0 0 1px rgba(139, 92, 246, 0.1),
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 60px -15px rgba(139, 92, 246, 0.4);
            overflow: hidden;
        }

        .omnisearch-input-container {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--omnisearch-border);
        }

        .omnisearch-search-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            color: var(--omnisearch-text-muted);
            flex-shrink: 0;
        }

        .omnisearch-search-icon svg {
            width: 20px;
            height: 20px;
        }

        .omnisearch-input {
            flex: 1;
            margin-left: 12px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 16px;
            color: var(--omnisearch-text);
        }

        .omnisearch-input::placeholder {
            color: var(--omnisearch-text-muted);
        }

        .omnisearch-shortcut-hint {
            display: flex;
            gap: 4px;
        }

        .omnisearch-shortcut-hint kbd,
        .omnisearch-footer kbd {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 4px;
            font-size: 11px;
            font-family: inherit;
            color: var(--omnisearch-text-muted);
        }

        /* Loading State */
        .omnisearch-loading {
            display: flex;
            justify-content: center;
            padding: 24px;
        }

        .omnisearch-spinner {
            width: 24px;
            height: 24px;
            border: 2px solid var(--omnisearch-accent);
            border-top-color: var(--omnisearch-primary);
            border-radius: 50%;
            animation: omnisearch-spin 0.6s linear infinite;
        }

        @keyframes omnisearch-spin {
            to { transform: rotate(360deg); }
        }

        /* Recent Searches */
        .omnisearch-recent {
            border-bottom: 1px solid var(--omnisearch-border);
        }

        .omnisearch-recent-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px 4px;
        }

        .omnisearch-recent-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--omnisearch-text-muted);
        }

        .omnisearch-clear-btn {
            background: none;
            border: none;
            padding: 4px 8px;
            font-size: 11px;
            color: var(--omnisearch-text-muted);
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.15s ease;
        }

        .omnisearch-clear-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--omnisearch-text);
        }

        .omnisearch-results {
            max-height: 400px;
            overflow-y: auto;
            padding: 8px;
        }

        .omnisearch-group {
            margin-bottom: 8px;
        }

        .omnisearch-group:last-child {
            margin-bottom: 0;
        }

        .omnisearch-group-label {
            padding: 8px 12px 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--omnisearch-primary);
        }

        .omnisearch-result {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px 12px;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s ease;
            color: inherit;
        }

        .omnisearch-result:hover,
        .omnisearch-result--selected {
            background: rgba(139, 92, 246, 0.15);
        }

        .omnisearch-result--selected {
            box-shadow: inset 0 0 0 1px rgba(139, 92, 246, 0.4);
        }

        .omnisearch-result-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            flex-shrink: 0;
        }

        .omnisearch-result-icon svg {
            width: 16px;
            height: 16px;
            color: var(--omnisearch-primary);
        }

        .omnisearch-result-content {
            flex: 1;
            margin-left: 12px;
            min-width: 0;
        }

        .omnisearch-result-title {
            font-size: 14px;
            font-weight: 500;
            color: var(--omnisearch-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .omnisearch-result-description {
            font-size: 12px;
            color: var(--omnisearch-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .omnisearch-result-action {
            flex-shrink: 0;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .omnisearch-result:hover .omnisearch-result-action,
        .omnisearch-result--selected .omnisearch-result-action {
            opacity: 1;
        }

        .omnisearch-result-action svg {
            width: 16px;
            height: 16px;
            color: var(--omnisearch-text-muted);
        }

        .omnisearch-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 20px;
            text-align: center;
            color: var(--omnisearch-text-muted);
            font-size: 14px;
        }

        .omnisearch-empty-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .omnisearch-empty-icon svg {
            width: 48px;
            height: 48px;
        }

        .omnisearch-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--omnisearch-border);
        }

        .omnisearch-footer-hints {
            display: flex;
            gap: 16px;
            font-size: 12px;
            color: var(--omnisearch-text-muted);
        }

        .omnisearch-footer-hints span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Toast */
        .omnisearch-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: var(--omnisearch-primary);
            color: white;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .omnisearch-toast svg {
            width: 18px;
            height: 18px;
        }

        /* Skeleton Loading */
        .omnisearch-skeleton {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.05) 25%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0.05) 75%
            );
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</div>
