@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">DTR Uploads</h1>
    <table class="admin-table min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-2 py-1">ID</th>
                <th class="px-2 py-1">Filename</th>
                <th class="px-2 py-1">Status</th>
                <th class="px-2 py-1">Uploaded At</th>
                <th class="px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uploads as $upload)
            <tr>
                <td class="px-2 py-1">{{ $upload->id }}</td>
                <td class="px-2 py-1">{{ $upload->filename }}</td>
                <td class="px-2 py-1">{{ ucfirst($upload->status) }}</td>
                <td class="px-2 py-1">{{ $upload->created_at }}</td>
                <td class="px-2 py-1 flex gap-2 items-center">
                    <a href="{{ route('admin.dtr.uploads.view', $upload->id) }}" class="text-blue-600 underline">View</a>
                    <form method="POST" action="{{ route('admin.dtr.uploads.delete', $upload->id) }}" onsubmit="return confirm('Are you sure you want to delete this DTR upload?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 underline ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
