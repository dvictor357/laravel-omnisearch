<div
    x-data="{
        open: @entangle('isOpen'),
        selectedIndex: @entangle('selectedIndex'),
        init() {
            // Global keyboard listener
            window.addEventListener('keydown', (e) => {
                // Cmd+K or Ctrl+K to open
                if ((e.metaKey || e.ctrlKey) && e.key === '{{ config('omnisearch.shortcut', 'k') }}') {
                    e.preventDefault();
                    this.open = true;
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                }

                // Escape to close
                if (e.key === 'Escape' && this.open) {
                    e.preventDefault();
                    this.open = false;
                }
            });
        },
        handleKeydown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                $wire.selectNext();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                $wire.selectPrevious();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                $wire.selectResult();
            }
        }
    }"
    x-on:omnisearch:navigate.window="window.location.href = $event.detail.url"
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
>
    {{-- Backdrop --}}
    <div
        class="omnisearch-backdrop"
        x-on:click="open = false"
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
            <svg class="omnisearch-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input
                x-ref="searchInput"
                type="text"
                wire:model.live.debounce.{{ config('omnisearch.ui.debounce', 300) }}ms="query"
                placeholder="{{ config('omnisearch.ui.placeholder', 'Search anything...') }}"
                class="omnisearch-input"
                x-on:keydown="handleKeydown"
            />
            @if(config('omnisearch.ui.show_keyboard_hints', true))
                <div class="omnisearch-shortcut-hint">
                    <kbd>esc</kbd>
                </div>
            @endif
        </div>

        {{-- Results --}}
        @if(!empty($groupedResults))
            <div class="omnisearch-results">
                @php $flatIndex = 0; @endphp
                @foreach($groupedResults as $group => $items)
                    <div class="omnisearch-group">
                        <div class="omnisearch-group-label">{{ $group }}</div>
                        @foreach($items as $result)
                            <button
                                type="button"
                                class="omnisearch-result {{ $selectedIndex === $flatIndex ? 'omnisearch-result--selected' : '' }}"
                                wire:click="navigateTo('{{ $result['url'] }}')"
                                x-on:mouseenter="selectedIndex = {{ $flatIndex }}"
                            >
                                <div class="omnisearch-result-icon">
                                    @switch($result['icon'])
                                        @case('user')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                            @break
                                        @case('link')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                                            @break
                                        @case('command')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                            @break
                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                    @endswitch
                                </div>
                                <div class="omnisearch-result-content">
                                    <div class="omnisearch-result-title">{{ $result['title'] }}</div>
                                    @if($result['description'])
                                        <div class="omnisearch-result-description">{{ $result['description'] }}</div>
                                    @endif
                                </div>
                                <div class="omnisearch-result-action">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                </div>
                            </button>
                            @php $flatIndex++; @endphp
                        @endforeach
                    </div>
                @endforeach
            </div>
        @elseif($query !== '')
            <div class="omnisearch-empty">
                <p>No results found for "<strong>{{ $query }}</strong>"</p>
            </div>
        @else
            <div class="omnisearch-empty">
                <p>Start typing to search...</p>
            </div>
        @endif

        {{-- Footer --}}
        @if(config('omnisearch.ui.show_keyboard_hints', true))
            <div class="omnisearch-footer">
                <div class="omnisearch-footer-hints">
                    <span><kbd>&uarr;</kbd><kbd>&darr;</kbd> to navigate</span>
                    <span><kbd>enter</kbd> to select</span>
                    <span><kbd>esc</kbd> to close</span>
                </div>
            </div>
        @endif
    </div>

<style>
    [x-cloak] { display: none !important; }

    .omnisearch-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 15vh;
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
        background: rgba(30, 30, 46, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 16px;
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
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .omnisearch-search-icon {
        width: 20px;
        height: 20px;
        color: rgba(255, 255, 255, 0.4);
        flex-shrink: 0;
    }

    .omnisearch-input {
        flex: 1;
        margin-left: 12px;
        background: transparent;
        border: none;
        outline: none;
        font-size: 16px;
        color: #fff;
    }

    .omnisearch-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
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
        color: rgba(255, 255, 255, 0.6);
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
        color: rgba(139, 92, 246, 0.8);
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
        color: rgba(139, 92, 246, 1);
    }

    .omnisearch-result-content {
        flex: 1;
        margin-left: 12px;
        min-width: 0;
    }

    .omnisearch-result-title {
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .omnisearch-result-description {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
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
        color: rgba(255, 255, 255, 0.4);
    }

    .omnisearch-empty {
        padding: 32px 20px;
        text-align: center;
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
    }

    .omnisearch-footer {
        padding: 12px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .omnisearch-footer-hints {
        display: flex;
        gap: 16px;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.4);
    }

    .omnisearch-footer-hints span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
</style>
</div>
