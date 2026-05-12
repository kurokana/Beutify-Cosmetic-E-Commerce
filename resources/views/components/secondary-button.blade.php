<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-lg font-semibold text-sm text-slate-700 shadow-light hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-200 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
