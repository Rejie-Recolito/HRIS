@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">
        {{ __('Leave Application Form') }}
    </h2>
@endsection


@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mt-8 mb-4">Leave Application Details</h2>

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Last Name</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->lastname }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">First Name</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->firstname }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Middle Name</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->middlename }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Date of Filing</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->date_of_filing }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Position</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->position }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Salary</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->salary }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Type of Leave</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->type_of_leave }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Others</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->others }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Number of Days</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->number_of_days }}</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 font-bold">Inclusive Dates</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $leaveApplication->inclusive_dates }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                        @if($leaveApplication->status === 'Submitted')
                            <form method="POST" action="{{ route('leave.accept', $leaveApplication->id) }}" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded">Accept</button>
                            </form>
                        @elseif($leaveApplication->status === 'Under Review')
                            <form method="POST" action="{{ route('leave.approve', $leaveApplication->id) }}" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('leave.deny', $leaveApplication->id) }}" style="display:inline-block; margin-left: 5px;">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Deny</button>
                            </form>
                            <form method="GET" action="{{ route('leave.generate-docx', $leaveApplication->id) }}" style="display:inline-block; margin-left: 5px;">
                                @csrf
                                <button type="submit" class="bg-yellow-600 text-white px-2 py-1 rounded">Download PDF</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('leave.delete', $leaveApplication->id) }}" style="display:inline-block; margin-left: 5px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-gray-600 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection



