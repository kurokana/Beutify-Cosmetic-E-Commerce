@php
    $toasts = [];

    if (session('success')) {
        $toasts[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'message' => session('error')];
    }
    if (session('warning')) {
        $toasts[] = ['type' => 'warning', 'message' => session('warning')];
    }
    if (session('info')) {
        $toasts[] = ['type' => 'info', 'message' => session('info')];
    }
@endphp

@if (count($toasts) > 0)
<div
    x-data="{
        toasts: {{ json_encode($toasts) }},
        removeToast(index) {
            this.toasts.splice(index, 1);
        }
    }"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none"
    aria-live="polite"
    aria-atomic="false"
    role="region"
    aria-label="Notifikasi"
>
    <template x-for="(toast, index) in toasts" :key="index">
        <div
            x-data="{ visible: false }"
            x-init="
                $nextTick(() => {
                    visible = true;
                    setTimeout(() => {
                        visible = false;
                        setTimeout(() => removeToast(index), 300);
                    }, 4000);
                })
            "
            x-show="visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border"
            :class="{
                'bg-green-50 border-green-200 text-green-800': toast.type === 'success',
                'bg-red-50 border-red-200 text-red-800': toast.type === 'error',
                'bg-yellow-50 border-yellow-200 text-yellow-800': toast.type === 'warning',
                'bg-blue-50 border-blue-200 text-blue-800': toast.type === 'info'
            }"
            role="alert"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0 mt-0.5">
                {{-- Success --}}
                <svg x-show="toast.type === 'success'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{-- Error --}}
                <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{-- Warning --}}
                <svg x-show="toast.type === 'warning'" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{-- Info --}}
                <svg x-show="toast.type === 'info'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Message --}}
            <p class="flex-1 text-sm font-medium leading-snug" x-text="toast.message"></p>

            {{-- Close Button --}}
            <button
                @click="visible = false; setTimeout(() => removeToast(index), 300)"
                class="flex-shrink-0 ml-1 opacity-60 hover:opacity-100 transition-opacity focus:outline-none"
                aria-label="Tutup notifikasi"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
@endif
