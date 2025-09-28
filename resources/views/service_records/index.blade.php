@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-3xl font-bold mb-4">Service Records</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-400">
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Name</th>
                <th class="border border-gray-300 px-4 py-2">Age</th>
                <th class="border border-gray-300 px-4 py-2">Salary</th>
                <th class="border border-gray-300 px-4 py-2">Date of Birth</th>
                <th class="border border-gray-300 px-4 py-2">Job Title</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($serviceRecords as $record)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->age }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->salary }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->date_of_birth }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->job_title }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="#" class="text-blue-500">Edit</a> |
                        <a href="#" class="text-red-500">Delete</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No service records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection