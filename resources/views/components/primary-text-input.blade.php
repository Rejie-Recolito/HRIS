@props([
'label' => 'label',
'name' => 'name',
// 'error'=> "",
'type'=> 'text',
'required'=> true,
'options' =>[]
])

@if ($type==='select')
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
        <label for="{{ $name }}" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">{{ $label }} :</label>
        <select name="{{ $name }}" wire:model.defer="{{ $name }}" id="{{ $name }}" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" :required={{ $required }}>
            <option value="">Select type</option>
            @foreach ($options as $option)
            <option value="{{ $option }}">{{$option}}</option>
            @endforeach
        </select>
        @error('{{ $name }}') <span class="text-red-600 mt-2">{{ $message }}</span> @enderror
    </div>

@else
<div class="mb-4 flex flex-col sm:flex-row sm:items-center">
    <label for="{{ $name }}" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">{{ $label }} :</label>
    <input 
        type="{{ $type }}" 
        name="{{ $name }}"
        id="{{ $name }}"
        class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" 
        :required="{{ $required }}">
    @error($name) <span class="text-red-600 mt-2">{{ $message }}</span> @enderror
</div>
@endif


