<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
<div>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <h3 class="text-lg font-bold mb-4">Leave Applications Table</h3>
     <table class="admin-table mb-6">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date of Filing</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaveApplications as $leave)
                <tr>
                    <td>{{ $leave->lastname }} {{ $leave->firstname }} has requested a Leave Application</td>
                    <td>{{ $leave->date_of_filing }}</td>
                    <td>{{ $leave->status }}</td>
                    <td>
                        <a href="{{ route('leave_application.view', ['id' => $leave->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded-md">View Leave Form</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No leave applications found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
