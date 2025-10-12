@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-black-300 dark:text-white']) }}>
    {{ $value ?? $slot }}
</label>
