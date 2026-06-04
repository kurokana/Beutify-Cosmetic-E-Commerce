<div
    x-data="{
        loading: false,
        init() {
            // Show spinner on navigation (link clicks and form submits)
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (
                    link &&
                    !link.hasAttribute('target') &&
                    !link.getAttribute('href').startsWith('#') &&
                    !link.getAttribute('href').startsWith('javascript') &&
                    !e.ctrlKey && !e.metaKey && !e.shiftKey
                ) {
                    this.loading = true;
                }
            });

            document.addEventListener('submit', (e) => {
                if (e.target.tagName === 'FORM') {
                    this.loading = true;
                }
            });

            // Hide on page show (back/forward navigation)
            window.addEventListener('pageshow', () => {
                this.loading = false;
            });
        }
    }"
    x-show="loading"
    x-transition:enter="transition-opacity duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9998] flex items-center justify-center bg-dark-secondary bg-opacity-60 backdrop-blur-sm"
    aria-live="polite"
    aria-label="Memuat halaman..."
    role="status"
>
    <div class="flex flex-col items-center gap-3">
        {{-- Spinner --}}
        <div class="relative w-12 h-12">
            <div class="absolute inset-0 rounded-full border-4 border-pink-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-pink-500 animate-spin"></div>
        </div>
        <p class="text-sm text-warm-gray font-medium">Memuat...</p>
    </div>
</div>
