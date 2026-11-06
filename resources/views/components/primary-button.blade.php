<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-[#16452F] border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-[#113725] focus:bg-[#113725] active:bg-[#0D2B20] focus:outline-none focus:ring-2 focus:ring-[#16452F]/60 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
