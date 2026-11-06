@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-[#3F443C]']) }}>
    {{ $value ?? $slot }}
</label>
