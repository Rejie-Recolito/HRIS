@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-black-300 dark:text-gray-800']) }}>
    {{ $value ?? $slot }}
</label>
