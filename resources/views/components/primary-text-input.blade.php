@props([
    'label' => 'label',
    'name' => 'name',
    'type'=> 'text',
    'required'=> true,
    'options' => [],
    'value' => null,
])

@php
    // Normalize required for attribute rendering
    $requiredAttr = $required ? 'required' : null;
    // For @error directive we need the bare name (no quotes)
    $errorName = $name;
    // Merge any extra attributes passed to the component
@endphp

@if ($type === 'select')
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
        <label for="{{ $name }}" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">{{ $label }} :</label>
        <select name="{{ $name }}" id="{{ $name }}" {{ $requiredAttr }} {{ $attributes->merge(['class' => 'flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm']) }}>
            <option value="">Select type</option>
            @foreach ($options as $option)
                <option value="{{ $option }}" @if((string)$option === (string)($value ?? '')) selected @endif>{{ $option }}</option>
            @endforeach
        </select>
        @error($errorName) <span class="text-red-600 mt-2">{{ $message }}</span> @enderror
    </div>

@else
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
        <label for="{{ $name }}" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">{{ $label }} :</label>
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value ?? '') }}"
            {{ $requiredAttr }}
            {{ $attributes->merge(['class' => 'flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm']) }}
        />
        @error($errorName) <span class="text-red-600 mt-2">{{ $message }}</span> @enderror
    </div>
@endif


