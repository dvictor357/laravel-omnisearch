function omnisearch() {
    return {
        open: @entangle('isOpen'),
        query: @entangle('query'),
        results: @json($results ?? []),
        groupedResults: @json($groupedResults ?? []),
        selectedIndex: @entangle('selectedIndex'),
        recentSearches: @json($recentSearches ?? []),
        recentSelectedIndex: -1,
        recentVisible: true,
        isLoading: @entangle('isLoading'),
        showCopyToastVisible: false,

        // Get keyboard shortcuts from config
        shortcuts: @json(config('omnisearch.ui.shortcuts', ['k'])),
        modifier: @json(config('omnisearch.ui.modifier', 'cmd')),

        init() {
            this.loadRecentSearches();

            // Global keyboard listener
            window.addEventListener('keydown', (e) => {
                // Open modal with shortcut
                if (this.matchesShortcut(e)) {
                    e.preventDefault();
                    this.open ? this.close() : this.open();
                }

                // Escape to close
                if (e.key === 'Escape' && this.open) {
                    e.preventDefault();
                    this.close();
                }
            });

            // Listen for recent searches updates from Livewire
            this.$watch('recentSearches', (value) => {
                this.saveRecentSearches(value);
            });
        },

        matchesShortcut(e) {
            const shortcut = this.shortcuts[0];
            const mod = this.modifier.toLowerCase();

            if (mod === 'cmd' || mod === 'meta') {
                return (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === shortcut.toLowerCase();
            } else if (mod === 'ctrl') {
                return e.ctrlKey && e.key.toLowerCase() === shortcut.toLowerCase();
            } else if (mod === 'alt') {
                return e.altKey && e.key.toLowerCase() === shortcut.toLowerCase();
            }

            return (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === shortcut.toLowerCase();
        },

        get shortcutDisplay() {
            const shortcut = this.shortcuts[0];
            const mod = this.modifier.toLowerCase();

            if (mod === 'cmd' || mod === 'meta') {
                return navigator.platform.includes('Mac') ? '⌘' + shortcut : 'Ctrl+' + shortcut;
            }

            return shortcut.toUpperCase();
        },

        get themeStyles() {
            return `
                --omnisearch-primary: ${this.getConfigValue('primary', '#8b5cf6')};
                --omnisearch-bg: ${this.getConfigValue('bg', 'rgba(30, 30, 46, 0.85)')};
                --omnisearch-radius: ${this.getConfigValue('radius', '16px')};
                --omnisearch-accent: ${this.getConfigValue('accent', 'rgba(139, 92, 246, 0.3)')};
            `;
        },

        getConfigValue(key, defaultValue) {
            const config = @json(config('omnisearch.ui', []));
            return config[key] || config['theme']?.[key] || defaultValue;
        },

        handleKeydown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.recentVisible ? this.selectRecentNext() : this.selectNext();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.recentVisible ? this.selectRecentPrevious() : this.selectPrevious();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                this.recentVisible ? this.selectRecentResult() : this.selectResult();
            } else if (e.key === 'Tab') {
                // Toggle between recent and results
                e.preventDefault();
                if (this.recentSearches.length > 0) {
                    this.recentVisible = !this.recentVisible;
                    if (this.recentVisible) {
                        this.selectedIndex = 0;
                    }
                }
            }
        },

        selectNext() {
            const total = this.results.length;
            if (total === 0) return;

            this.selectedIndex = (this.selectedIndex + 1) % total;
            this.scrollSelectedIntoView();
        },

        selectPrevious() {
            const total = this.results.length;
            if (total === 0) return;

            this.selectedIndex = this.selectedIndex > 0 ? this.selectedIndex - 1 : total - 1;
            this.scrollSelectedIntoView();
        },

        selectRecentNext() {
            const total = this.recentSearches.length;
            if (total === 0) return;

            this.recentSelectedIndex = (this.recentSelectedIndex + 1) % total;
        },

        selectRecentPrevious() {
            const total = this.recentSearches.length;
            if (total === 0) return;

            this.recentSelectedIndex = this.recentSelectedIndex > 0 ? this.recentSelectedIndex - 1 : total - 1;
        },

        scrollSelectedIntoView() {
            this.$nextTick(() => {
                const selected = this.$el.querySelector('.omnisearch-result--selected');
                if (selected) {
                    selected.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            });
        },

        selectResult() {
            if (this.results.length === 0) return;

            const result = this.results[this.selectedIndex];
            if (!result) return;

            this.addToRecentSearches(result);
            @this.selectResult();
        },

        selectRecent(result) {
            this.addToRecentSearches(result);
            @this.selectRecent(result);
        },

        addToRecentSearches(result) {
            // Remove if already exists
            const existing = this.recentSearches.findIndex(r => r.id === result.id);
            if (existing > -1) {
                this.recentSearches.splice(existing, 1);
            }

            // Add to beginning
            this.recentSearches.unshift(result);

            // Limit to 10 recent searches
            if (this.recentSearches.length > 10) {
                this.recentSearches = this.recentSearches.slice(0, 10);
            }

            this.saveRecentSearches(this.recentSearches);
        },

        clearRecentSearches() {
            this.recentSearches = [];
            this.recentSelectedIndex = -1;
            localStorage.removeItem('omnisearch_recent');
            @this.clearRecentSearches();
        },

        loadRecentSearches() {
            try {
                const stored = localStorage.getItem('omnisearch_recent');
                if (stored) {
                    this.recentSearches = JSON.parse(stored);
                }
            } catch (e) {
                console.warn('Failed to load recent searches:', e);
            }
        },

        saveRecentSearches(searches) {
            try {
                localStorage.setItem('omnisearch_recent', JSON.stringify(searches));
            } catch (e) {
                console.warn('Failed to save recent searches:', e);
            }
        },

        showCopyToast() {
            this.showCopyToastVisible = true;
            setTimeout(() => {
                this.showCopyToastVisible = false;
            }, 2000);
        },

        close() {
            this.open = false;
            this.recentVisible = true;
            this.recentSelectedIndex = -1;
            @this.close();
        }
    };
}
