@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block ps-3 pe-4 py-2 mx-2 border border-[#198f51] text-start text-base font-medium text-[#198f51] dark:text-white bg-white dark:bg-gray-700 rounded-lg focus:outline-none focus:text-[#198f51] dark:focus:text-white focus:bg-white dark:focus:bg-gray-700 focus:border-[#198f51] transition duration-150 ease-in-out'
            : 'block ps-3 pe-4 py-2 mx-2 border border-transparent text-start text-base font-medium text-white hover:text-white hover:bg-green-700 hover:border-gray-300 focus:outline-none focus:text-white focus:bg-green-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="flex items-center gap-1">{{ $slot }}</span>
</a>
