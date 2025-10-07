<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
<div>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <h3 class="text-lg font-bold mb-4">Leave Applications Table</h3>
    <table class="min-w-full border text-sm mb-6">
        <thead>
            <tr class="bg-gray-200 dark:bg-gray-700">
                <th class="border px-2 py-1">LASTNAME</th>
                <th class="border px-2 py-1">FIRSTNAME</th>
                <th class="border px-2 py-1">MIDDLENAME</th>
                <th class="border px-2 py-1">Date of Filing</th>
                <th class="border px-2 py-1">Position</th>
                <th class="border px-2 py-1">Salary</th>
                <th class="border px-2 py-1">Type of Leave</th>
                <th class="border px-2 py-1">Others</th>
                <th class="border px-2 py-1">Number of Days</th>
                <th class="border px-2 py-1">Inclusive Dates</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaveApplications as $leave)
                <tr>
                    <td class="border px-2 py-1">{{ $leave->lastname }}</td>
                    <td class="border px-2 py-1">{{ $leave->firstname }}</td>
                    <td class="border px-2 py-1">{{ $leave->middlename }}</td>
                    <td class="border px-2 py-1">{{ $leave->date_of_filing }}</td>
                    <td class="border px-2 py-1">{{ $leave->position }}</td>
                    <td class="border px-2 py-1">{{ $leave->salary }}</td>
                    <td class="border px-2 py-1">{{ $leave->type_of_leave }}</td>
                    <td class="border px-2 py-1">{{ $leave->others }}</td>
                    <td class="border px-2 py-1">{{ $leave->number_of_days }}</td>
                    <td class="border px-2 py-1">{{ $leave->inclusive_dates }}</td>
                    <td class="border px-2 py-1">{{ $leave->status }}</td>
                    <td class="border px-2 py-1">
                        @if($leave->status === 'Submitted')
                            <button type="button" wire:click="accept({{ $leave->id }})" class="bg-blue-600 text-white px-2 py-1 rounded">Accept</button>
                        @elseif($leave->status === 'Under Review')
                            <button type="button" wire:click="approve({{ $leave->id }})" class="bg-green-600 text-white px-2 py-1 rounded">Approve</button>
                            <button type="button" wire:click="deny({{ $leave->id }})" class="bg-red-600 text-white px-2 py-1 rounded">Deny</button>
                            <form method="GET" action="{{ route('leave.generate-docx', $leave->id) }}" style="display:inline-block; margin-left: 5px;">
                                @csrf
                                <button type="submit" class="bg-yellow-600 text-white px-2 py-1 rounded">Download PDF</button>
                            </form>
                        @endif
                        <button type="button" wire:click="delete({{ $leave->id }})" class="bg-gray-600 text-white px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if(empty($leaveApplications) || count($leaveApplications) === 0)
        <div class="text-center text-gray-500">No leave applications found.</div>
    @endif
</div>
