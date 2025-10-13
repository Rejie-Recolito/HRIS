@props(['active'])

@php

$classes = ($active ?? false)
    ? 'block ps-3 pe-4 py-2 mx-2 border-2 border-green-600 !border-green-600 text-start text-base font-medium bg-white text-[#198f51] dark:bg-[#1c1c1d] dark:text-white rounded-lg focus:outline-none focus:text-[#198f51] dark:focus:text-white focus:border-green-600 dark:focus:border-green-600 transition duration-150 ease-in-out'
    : 'block ps-3 pe-4 py-2 mx-2 border border-transparent text-start text-base font-medium text-white hover:text-white hover:bg-green-700 hover:border-gray-300 focus:outline-none focus:text-white focus:bg-green-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>