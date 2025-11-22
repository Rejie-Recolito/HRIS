@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        {{ __('Leave Application Form') }}
    </h2>
@endsection


@section('content')
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Two column layout: Leave Application Details (50%) and Employee Info (50%) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Left Column: Leave Application Details Table -->
                        <div>
                            <div class="overflow-x-auto">
                                <table class="admin-table">
                                    <tbody>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Last Name</td>
                                            <td>{{ $leaveApplication->lastname }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">First Name</td>
                                            <td>{{ $leaveApplication->firstname }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Middle Name</td>
                                            <td>{{ $leaveApplication->middlename }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Date of Filing</td>
                                            <td>{{ $leaveApplication->date_of_filing }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Position</td>
                                            <td>{{ $leaveApplication->position }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Salary</td>
                                            <td>{{ $leaveApplication->salary }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Type of Leave</td>
                                            <td>{{ $leaveApplication->type_of_leave }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Others</td>
                                            <td>{{ $leaveApplication->others }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Number of Days</td>
                                            <td>{{ $leaveApplication->number_of_days }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" style="background-color: #198f51; color: #ffffff; border-color: #ffffff;">Inclusive Dates</td>
                                            <td>{{ $leaveApplication->inclusive_dates }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right Column: Leave Card Summary -->
                        <div>
                            <div class="bg-white dark:bg-[#282828] rounded-lg p-4">
                                <h4 class="text-lg font-bold mt-5 mb-8">EMPLOYEE'S LEAVE CARD</h4>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <table class="admin-table w-full">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center text-md" style="background-color: #198f51; color: #ffffff;">Vacation Leave</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Opening Balance</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ $leaveCardDetails['vacation']['opening'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Earned Credits</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ number_format($leaveCardDetails['vacation']['earned'] ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Total Earned</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ number_format($leaveCardDetails['vacation']['total'] ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Availed</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ $leaveCardDetails['vacation']['availed'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold text-md py-1 text-[#198f51]" style="width: 70%;">BALANCE</td>
                                                <td class="text-right font-bold text-md py-1" style="color: #198f51; width: 30%;">{{ number_format($leaveCardDetails['vacation']['balance'] ?? 0, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="admin-table w-full">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center text-md" style="background-color: #198f51; color: #ffffff;">Sick Leave</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Opening Balance</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ $leaveCardDetails['sick']['opening'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Earned Credits</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ number_format($leaveCardDetails['sick']['earned'] ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Total Earned</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ number_format($leaveCardDetails['sick']['total'] ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-md py-1" style="width: 70%;">Availed</td>
                                                <td class="text-right font-semibold text-md py-1" style="width: 30%;">{{ $leaveCardDetails['sick']['availed'] ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold text-md py-1 text-[#198f51]" style="width: 70%;">Balance</td>
                                                <td class="text-right font-bold text-md py-1" style="color: #198f51; width: 30%;">{{ number_format($leaveCardDetails['sick']['balance'] ?? 0, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                @if(isset($leaveApplication->cert_vacation) || isset($leaveApplication->cert_sick))
                                    <hr class="my-3 border-gray-200 dark:border-gray-700" />
                                    <div class="text-xs text-gray-500">Certification flags set: 
                                        @if($leaveApplication->cert_vacation) Vacation @endif
                                        @if($leaveApplication->cert_sick) Sick @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

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

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded dark:bg-red-900 dark:border-red-700 dark:text-red-200">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Action form  -->
                    <form id="actionForm" x-data="{ certType: '{{ old('cert_leave_type', ($leaveApplication->cert_vacation ? 'vacation' : ($leaveApplication->cert_sick ? 'sick' : '')) ) }}' }" method="POST" action="{{ route('leave.action.update', $leaveApplication->id) }}#actionForm" class="mt-6 border rounded bg-gray-50 dark:bg-[#282828] dark:border-gray-700" >
                        @csrf
                        <!-- record which credit type the admin is setting -->
                        <input type="hidden" name="cert_leave_type" :value="certType" />
                        <h3 class="font-semibold mt-6 mb-4 py-2 text-center text-white" style="background-color: #198f51;"> DETAILS OF ACTION ON APPLICATION</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 pb-4">
                            <div class="border p-3 border-[#198f51]">
                                <h4 class="font-medium mb-2 dark:text-gray-100"> CERTIFICATION OF LEAVE CREDITS</h4>
                                <div class="mb-2 flex items-center gap-2">
                                    <label class="text-md dark:text-gray-300 whitespace-nowrap">As of</label>
                                    <input type="date" name="cert_as_of" value="{{ old('cert_as_of', $leaveApplication->cert_as_of ? $leaveApplication->cert_as_of->format('Y-m-d') : '') }}" class="border-[#198f51] input-field-border custom-input rounded-xl flex-1 dark:bg-gray-700 dark:text-gray-100" />
                                </div>
                                

                                <div class="mt-3">
                                    <label class="inline-flex items-center mr-4 dark:text-gray-300">
                                        <input type="radio" x-model="certType" name="cert_leave_type_radio" value="vacation" class="mr-2" /> Show Vacation Leave Credits
                                    </label>
                                    <label class="inline-flex items-center dark:text-gray-300">
                                        <input type="radio" x-model="certType" name="cert_leave_type_radio" value="sick" class="mr-2" /> Show Sick Leave Credits
                                    </label>
                                </div>
                            </div>

                            <div class="border p-3 border-[#198f51]">
                                <h4 class="font-medium mb-2 dark:text-gray-100"> RECOMMENDATION</h4>
                                <div class="mt-3">
                                    <label class="inline-flex items-center mr-4 dark:text-gray-300">
                                        <input type="radio" name="recommendation" value="For approval" {{ old('recommendation', $leaveApplication->recommendation) == 'For approval' ? 'checked' : '' }} class="mr-2" /> For approval
                                    </label>
                                    <label class="inline-flex items-center dark:text-gray-300">
                                        <input type="radio" name="recommendation" value="For disapproval" {{ old('recommendation', $leaveApplication->recommendation) == 'For disapproval' ? 'checked' : '' }} class="mr-2" /> For disapproval
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm dark:text-gray-300">If disapproval, state reason</label>
                                    <textarea name="recommendation_reason" rows="3" class="border-gray-300 input-field-border custom-input rounded-xl w-full dark:bg-gray-700 dark:text-gray-100">{{ old('recommendation_reason', $leaveApplication->recommendation_reason) }}</textarea>
                                </div>
                                <div class="mt-4">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm dark:text-gray-300 whitespace-nowrap">Authorized Officer Name</label>
                                        <input type="text" name="authorized_officer_recommendation" class="border-gray-300 input-field-border custom-input rounded-xl flex-1 dark:bg-gray-700 dark:text-gray-100" value="{{ old('authorized_officer_recommendation', $leaveApplication->authorized_officer_recommendation) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 px-4 pb-4">
                            <div class="border p-3 border-[#198f51]" x-data="{
                                vacationBalance: {{ $leaveCardDetails['vacation']['balance'] ?? 0 }},
                                sickBalance: {{ $leaveCardDetails['sick']['balance'] ?? 0 }},
                                vacationLess: Number('{{ $leaveApplication->status === 'Approved' ? 0 : old('vacation_less_this_application', $leaveApplication->vacation_less_this_application ?? 0) }}'),
                                sickLess: Number('{{ $leaveApplication->status === 'Approved' ? 0 : old('sick_less_this_application', $leaveApplication->sick_less_this_application ?? 0) }}'),
                                get vacationNewBalance() { return this.vacationBalance - (Number(this.vacationLess) || 0) },
                                get sickNewBalance() { return this.sickBalance - (Number(this.sickLess) || 0) }
                            }">
                                <h4 class="font-medium mb-2 dark:text-gray-100">LEAVE CREDITS</h4>

                                <!-- Vacation credits panel -->
                                <div x-show="certType === 'vacation'">
                                    <table class="w-full border-collapse">
                                        <tr>
                                            <td class="pr-2 dark:text-gray-300">Total Earned (Vacation)</td>
                                            <td><input type="number" name="vacation_total_earned" readonly step="0.01" class="border-gray-300 input-field-border custom-input rounded-xl w-full bg-gray-100 dark:bg-gray-600 dark:text-gray-100" value="{{ $leaveCardDetails['vacation']['total'] ?? 0 }}" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2 dark:text-gray-300">Less this application (Vacation)</td>
                                            <td><input type="number" name="vacation_less_this_application" x-model.number="vacationLess" step="0.01" class="border-gray-300 input-field-border custom-input rounded-xl w-full dark:bg-gray-700 dark:text-gray-100" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2 dark:text-gray-300">Balance (Vacation)</td>
                                            <td><input type="number" name="vacation_balance" readonly step="0.01" class="border-gray-300 input-field-border custom-input rounded-xl w-full bg-gray-100 dark:bg-gray-600 dark:text-gray-100" x-bind:value="vacationNewBalance" /></td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Sick credits panel -->
                                <div x-show="certType === 'sick'">
                                    <table class="w-full border-collapse">
                                        <tr>
                                            <td class="pr-2 dark:text-gray-300">Total Earned (Sick)</td>
                                            <td><input type="number" name="sick_total_earned" readonly step="0.01" class="border-gray-300 input-field-border custom-input rounded-md w-full bg-gray-100 dark:bg-gray-600 dark:text-gray-100" value="{{ $leaveCardDetails['sick']['total'] ?? 0 }}" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2 dark:text-gray-300">Less this application (Sick)</td>
                                            <td><input type="number" name="sick_less_this_application" x-model.number="sickLess" step="0.01" class="border-gray-300 input-field-border custom-input rounded-md w-full dark:bg-gray-700 dark:text-gray-100" /></td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2 dark:text-gray-300">Balance (Sick)</td>
                                            <td><input type="number" name="sick_balance" readonly step="0.01" class="border-gray-300 input-field-border custom-input rounded-md w-full bg-gray-100 dark:bg-gray-600 dark:text-gray-100" x-bind:value="sickNewBalance" /></td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm dark:text-gray-300">Authorized Officer Name</label>
                                        <input type="text" name="authorized_officer_leave_cred" class="border-gray-300 input-field-border custom-input rounded-xl w-full dark:bg-gray-700 dark:text-gray-100" value="{{ old('authorized_officer_leave_cred', $leaveApplication->authorized_officer_leave_cred) }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="border p-3 border-[#198f51]">
                                <h4 class="font-medium mb-2 dark:text-gray-100"> APPROVED FOR /  DISAPPROVED DUE TO</h4>
                                <div class="mb-2">
                                    <label class="block text-sm dark:text-gray-300">Approved for</label>
                                    <input type="text" name="approved_days_with_pay" placeholder="______ days with pay" value="{{ old('approved_days_with_pay', $leaveApplication->approved_days_with_pay) }}" class="border-gray-300 input-field-border custom-input rounded-xl w-full mb-2 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400" />
                                    <input type="text" name="approved_days_without_pay" placeholder="______ days without pay" value="{{ old('approved_days_without_pay', $leaveApplication->approved_days_without_pay) }}" class="border-gray-300 input-field-border custom-input rounded-xl w-full mb-2 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400" />
                                    <input type="text" name="approved_others" placeholder="others (Specify)" value="{{ old('approved_others', $leaveApplication->approved_others) }}" class="border-gray-300 input-field-border custom-input rounded-xl w-full dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400" />
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm dark:text-gray-300">Disapproved due to</label>
                                    <textarea name="disapproved_reason" rows="3" class="border-gray-300 input-field-border custom-input rounded-xl w-full dark:bg-gray-700 dark:text-gray-100">{{ old('disapproved_reason', $leaveApplication->disapproved_reason) }}</textarea>
                                </div>
                                <div class="mt-4">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm dark:text-gray-300 whitespace-nowrap">Authorized Officer Name</label>
                                        <input type="text" name="authorized_officer" class="border-gray-300 input-field-border custom-input rounded-xl flex-1 dark:bg-gray-700 dark:text-gray-100" value="{{ old('authorized_officer', $leaveApplication->authorized_officer) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-4 ml-4">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">Save Action Details</button>
                        </div>
                    </form>
                    
                    <div class="mt-6">
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
                                <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Disapproved</button>
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
    </div>

    <script>
        // Restore scroll position immediately (before DOMContentLoaded)
        (function() {
            const savedPosition = sessionStorage.getItem('scrollPosition');
            if (savedPosition) {
                window.scrollTo(0, parseInt(savedPosition));
                sessionStorage.removeItem('scrollPosition');
            }
        })();

        // Save scroll position before form submission
        document.addEventListener('DOMContentLoaded', function() {
            const actionForm = document.querySelector('form[action*="leave.action.update"]');
            if (actionForm) {
                actionForm.addEventListener('submit', function() {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                });
            }
        });
    </script>
@endsection



