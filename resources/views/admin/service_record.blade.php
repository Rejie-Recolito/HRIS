@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">Service Record Requests</h2>
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
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $r)
                                <tr>
                                    <td>{{ $r->name }}</td>
                                    <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="flex items-center space-x-2">
                                        <form method="POST" action="{{ route('service-record-requests.accept', ['id' => $r->id]) }}" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Accept</button>
                                        </form>
                                        <form method="POST" action="{{ route('service-record-requests.destroy', ['id' => $r->id]) }}" style="display:inline-block;" onsubmit="return confirm('Delete this request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
