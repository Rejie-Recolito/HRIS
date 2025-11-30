<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
<div>
    <!-- Success Modal -->
    @if(session('success'))
        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-[#1c1c1d] p-8 rounded-lg shadow-lg border-2" style="border-color: #2bb16b; min-width: 400px;">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-3">Success!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 text-base">{{ session('success') }}</p>
                    <button onclick="document.getElementById('successModal').style.display='none'" class="custom-submit-btn px-6 py-2 rounded-md">Continue</button>
                </div>
            </div>
        </div>
    @endif

     <table class="admin-table mb-6">
        <thead>
            <tr>
                <th>NAME</th>
                <th style="text-align: center !important;">DATE OF FILING</th>
                <th style="text-align: center !important;">STATUS</th>
                <th style="text-align: center !important;">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaveApplications as $leave)
                <tr>
                    <td>{{ $leave->lastname }}, {{ $leave->firstname }} {{ $leave->middlename }}</td>
                    <td style="text-align: center !important;">{{ $leave->date_of_filing }}</td>
                    <td style="text-align: center !important;">{{ $leave->status }}</td>
                    <td style="text-align: center !important;">
                        <a href="{{ route('leave_application.view', ['id' => $leave->id]) }}" class="inline-flex items-center justify-center px-3 py-1 bg-[#198f51] hover:bg-[#166534] text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#166534] whitespace-nowrap">Leave Form</a>
                        <button type="button" onclick="if(!confirm('Delete this leave application from view?')) { event.stopImmediatePropagation(); return false; }" wire:click="delete({{ $leave->id }})" class="inline-flex items-center justify-center ml-2 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-700">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No current leave applications.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
