<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
<div>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <h3 class="text-lg font-bold mb-4">Leave Applications Table</h3>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Name</th>
                <th class="border border-gray-300 px-4 py-2">Date of Filing</th>
                <th class="border border-gray-300 px-4 py-2">Status</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaveApplications as $leave)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $leave->lastname }} {{ $leave->firstname }} has requested a Leave Application</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $leave->date_of_filing }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $leave->status }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="{{ route('leave_application.view', ['id' => $leave->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded-md">View Leave Form</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center border border-gray-300 px-4 py-2">No leave applications found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
