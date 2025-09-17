@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Leave Applications
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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
                                    <td class="border px-2 py-1">
                                        <form method="POST" action="{{ route('leave.accept', $leave->id) }}" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded">Accept</button>
                                        </form>
                                        <form method="POST" action="{{ route('leave.delete', $leave->id) }}" style="display:inline-block;">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($leaveApplications->isEmpty())
                        <div class="text-center text-gray-500">No leave applications found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
