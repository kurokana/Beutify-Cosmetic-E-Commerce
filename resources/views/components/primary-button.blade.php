<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-rose-400 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:bg-rose-500 focus:bg-rose-500 active:bg-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-300 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
