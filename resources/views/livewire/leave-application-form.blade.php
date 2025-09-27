<div>
    {{-- Stop trying to control. --}}
<div>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    @if($this->lastApplication && !in_array($this->lastApplication->status, ['Approved', 'Denied']))
        <div class="mb-4 text-blue-600">Status: {{ $this->lastApplication->status }}</div>
    @else
        <form wire:submit.prevent="submit">
            <div class="mb-4">
                <label for="lastname" class="block font-medium text-gray-800 dark:text-gray-100">LASTNAME</label>
                <input type="text" wire:model.defer="lastname" id="lastname" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('lastname') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="firstname" class="block font-medium text-gray-800 dark:text-gray-100">FIRSTNAME</label>
                <input type="text" wire:model.defer="firstname" id="firstname" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('firstname') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="middlename" class="block font-medium text-gray-800 dark:text-gray-100">MIDDLENAME</label>
                <input type="text" wire:model.defer="middlename" id="middlename" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('middlename') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="date_of_filing" class="block font-medium text-gray-800 dark:text-gray-100">Date of Filing</label>
                <input type="date" wire:model.defer="date_of_filing" id="date_of_filing" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('date_of_filing') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="position" class="block font-medium text-gray-800 dark:text-gray-100">Position</label>
                <input type="text" wire:model.defer="position" id="position" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('position') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="salary" class="block font-medium text-gray-800 dark:text-gray-100">Salary</label>
                <input type="number" step="0.01" wire:model.defer="salary" id="salary" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('salary') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="type_of_leave" class="block font-medium text-gray-800 dark:text-gray-100">Type of leave to be availed of</label>
                <select wire:model.defer="type_of_leave" id="type_of_leave" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                    <option value="">Select type</option>
                    <option value="Vacation leave">Vacation leave</option>
                    <option value="Mandatory/Forced Leave">Mandatory/Forced Leave</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Maternity Leave">Maternity Leave</option>
                    <option value="Paternity Leave">Paternity Leave</option>
                    <option value="Solo Parent Leave">Solo Parent Leave</option>
                    <option value="Study Leave">Study Leave</option>
                    <option value="10-Day VAWC Leave">10-Day VAWC Leave</option>
                    <option value="Rehabilitation Privilage">Rehabilitation Privilage</option>
                    <option value="Special Leave Benefits for Women">Special Leave Benefits for Women</option>
                    <option value="Special Emergency(Calamity) Leave">Special Emergency(Calamity) Leave</option>
                    <option value="Adoption Leave">Adoption Leave</option>
                </select>
                @error('type_of_leave') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="others" class="block font-medium text-gray-800 dark:text-gray-100">Others</label>
                <input type="text" wire:model.defer="others" id="others" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700">
                @error('others') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="number_of_days" class="block font-medium text-gray-800 dark:text-gray-100">Number of Working days Applied for</label>
                <input type="number" wire:model.defer="number_of_days" id="number_of_days" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('number_of_days') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label for="inclusive_dates" class="block font-medium text-gray-800 dark:text-gray-100">Inclusive Dates</label>
                <input type="text" wire:model.defer="inclusive_dates" id="inclusive_dates" class="w-full border rounded p-2 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700" required>
                @error('inclusive_dates') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit</button>
        </form>
    @endif
</div>
