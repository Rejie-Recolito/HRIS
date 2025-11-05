@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
        </svg>
        {{ __('SERVICE RECORD') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card-bg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mt-8 mb-4">Requests</h2>

                    <table class="admin-table mb-6">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serviceRecords as $record)
                                <tr>
                                    <td>{{ $record->name }} requested for Service Record</td>
                                    <td>
                                        <select class="border border-gray-300 rounded-md text-black" onchange="updateStatus(this, '{{ $record->id }}')">
                                            <option value="pending" {{ $record->request_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="ready" {{ $record->request_status === 'ready' ? 'selected' : '' }}>Ready To Claim</option>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="{{ route('service_record.request_form', ['id' => $record->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded-md">View Request Form</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <script>
                        function updateStatus(select, recordId) {
                            const status = select.value;
                            fetch(`/service-records/${recordId}/update-status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ status })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Failed to update status');
                                }
                                return response.json();
                            })
                            .then(data => {
                                alert('Status updated successfully');
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Failed to update status');
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
