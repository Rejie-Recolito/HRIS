@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('SERVICE RECORD') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mt-8 mb-4">Requests</h2>

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Name</th>
                                <th class="border border-gray-300 px-4 py-2">Status</th>
                                <th class="border border-gray-300 px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serviceRecords as $record)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $record->name }} requested for Service Record</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <select class="border border-gray-300 rounded-md text-black" onchange="updateStatus(this, {{ $record->id }})">
                                            <option value="pending" {{ $record->request_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="ready" {{ $record->request_status === 'ready' ? 'selected' : '' }}>Ready To Claim</option>
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('service_record.request_form', ['id' => $record->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded-md">View Request Form</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center border border-gray-300 px-4 py-2">No requests found.</td>
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
