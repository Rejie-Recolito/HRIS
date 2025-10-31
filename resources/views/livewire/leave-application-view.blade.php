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

                    <!-- Action form matching the attached image -->
                    <form x-data="{ certType: '{{ old('cert_leave_type', ($leaveApplication->cert_vacation ? 'vacation' : ($leaveApplication->cert_sick ? 'sick' : '')) ) }}' }" method="POST" action="{{ route('leave.action.update', $leaveApplication->id) }}" class="mt-6 p-4 border rounded bg-gray-50" >
                        @csrf
                        <!-- record which credit type the admin is setting -->
                        <input type="hidden" name="cert_leave_type" :value="certType" />
                        <h3 class="font-semibold mb-2"> DETAILS OF ACTION ON APPLICATION</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border p-3">
                                <h4 class="font-medium mb-2"> CERTIFICATION OF LEAVE CREDITS</h4>
                                <div class="mb-2">
                                    <label class="block text-sm">As of</label>
                                    <input type="date" name="cert_as_of" class="border-gray-300 input-field-border custom-input rounded-md w-full" />
                                </div>
                                

                                <div class="mt-3">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="radio" x-model="certType" name="cert_leave_type_radio" value="vacation" class="mr-2" /> Show Vacation Leave Credits
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" x-model="certType" name="cert_leave_type_radio" value="sick" class="mr-2" /> Show Sick Leave Credits
                                    </label>
                                </div>
                            </div>

                            <div class="border p-3">
                                <h4 class="font-medium mb-2"> RECOMMENDATION</h4>
                                <div class="mb-2">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="radio" name="recommendation" value="For approval" class="mr-2" /> For approval
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="recommendation" value="For disapproval" class="mr-2" /> For disapproval
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm">If disapproval, state reason</label>
                                    <textarea name="recommendation_reason" rows="3" class="border-gray-300 input-field-border custom-input rounded-md w-full"></textarea>
                                </div>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm">Authorized Officer Name</label>
                                        <input type="text" name="authorized_officer_recommendation" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('authorized_officer', $leaveApplication->authorized_officer) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border p-3">
                                <h4 class="font-medium mb-2">Leave Credits</h4>

                                <!-- Vacation credits panel -->
                                <div x-show="certType === 'vacation'">
                                    <table class="w-full border-collapse">
                                        <tr>
                                            <td class="pr-2">Total Earned (Vacation)</td>
                                            <td><input type="number" name="vacation_total_earned" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('vacation_total_earned', $leaveApplication->vacation_total_earned) }}" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Less this application (Vacation)</td>
                                            <td><input type="number" name="vacation_less_this_application" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('vacation_less_this_application', $leaveApplication->vacation_less_this_application) }}" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Balance (Vacation)</td>
                                            <td><input type="number" name="vacation_balance" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('vacation_balance', $leaveApplication->vacation_balance) }}" /></td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Sick credits panel -->
                                <div x-show="certType === 'sick'">
                                    <table class="w-full border-collapse">
                                        <tr>
                                            <td class="pr-2">Total Earned (Sick)</td>
                                            <td><input type="number" name="sick_total_earned" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('sick_total_earned', $leaveApplication->sick_total_earned) }}" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Less this application (Sick)</td>
                                            <td><input type="number" name="sick_less_this_application" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('sick_less_this_application', $leaveApplication->sick_less_this_application) }}" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Balance (Sick)</td>
                                            <td><input type="number" name="sick_balance" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('sick_balance', $leaveApplication->sick_balance) }}" /></td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm">Authorized Officer Name</label>
                                        <input type="text" name="authorized_officer_leave_cred" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('authorized_officer', $leaveApplication->authorized_officer) }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="border p-3">
                                <h4 class="font-medium mb-2"> APPROVED FOR /  DISAPPROVED DUE TO</h4>
                                <div class="mb-2">
                                    <label class="block text-sm">Approved for</label>
                                    <input type="text" name="approved_days_with_pay" placeholder="______ days with pay" class="border-gray-300 input-field-border custom-input rounded-md w-full mb-2" />
                                    <input type="text" name="approved_days_without_pay" placeholder="______ days without pay" class="border-gray-300 input-field-border custom-input rounded-md w-full mb-2" />
                                    <input type="text" name="approved_others" placeholder="others (Specify)" class="border-gray-300 input-field-border custom-input rounded-md w-full" />
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm">Disapproved due to</label>
                                    <textarea name="disapproved_reason" rows="3" class="border-gray-300 input-field-border custom-input rounded-md w-full"></textarea>
                                </div>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm">Authorized Officer Name</label>
                                        <input type="text" name="authorized_officer" class="border-gray-300 input-field-border custom-input rounded-md w-full" value="{{ old('authorized_officer', $leaveApplication->authorized_officer) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">Save Action Details</button>
                        </div>
                    </form>
                    
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



