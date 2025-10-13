@props(['active'])

@php

$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-white dark:border-white text-sm font-bold leading-5 text-white dark:text-white focus:outline-none focus:border-white dark:focus:border-white transition duration-150 ease-in-out rounded-t-md'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white dark:text-white focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
