<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#125047] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0E453F] focus:bg-[#0E453F] active:bg-[#0B3A33] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
