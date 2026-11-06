@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#C9C4B4] focus:border-[#16452F] focus:ring-[#16452F] rounded-xl shadow-sm py-2.5']) }}>
