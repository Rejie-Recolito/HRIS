@props([
'label' => 'label',
'name' => 'name',
// 'error'=> "",
'type'=> 'text',
'required'=> true,
'options' =>[]
])

@if ($type==='select')
    <div class="mb-4">
                <label for="{{ $name }}" class="block font-medium text-gray-800 dark:text-gray-100">Type of leave to be availed of</label>
                <select name="{{ $name }}" wire:model.defer="{{ $name }}" id="{{ $name }}" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" :required={{ $required }}>
                    <option :value="null">Select type</option>
                    @foreach ($options as $option)
                    <option value="{{ $option }}">{{$option}}</option>
                    @endforeach
                </select>
                @error('{{ $name }}') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>

@else
<div class="mb-4">
    <label for="{{ $name }}" class="block font-medium text-gray-800 dark:text-gray-100">{{ $label }}</label>
    <input 
        type="{{ $type }}" 
        name="{{ $name }}"
        id="{{ $name }}"
        class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" 
        :required="{{ $required }}">
    @error($name) <span class="text-red-600">{{ $message }}</span> @enderror
</div>
@endif


