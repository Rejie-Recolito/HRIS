@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Employee;

    $employee = null;
    if (Auth::check()) {
        // try to load related employee by user_id
        $employee = Employee::where('user_id', Auth::id())->first();
    }
@endphp

<div x-data="{ typeOfLeave: '', showSickDetails: '', vacationDetails: '', showOverlay: false, inclusiveFrom: '', inclusiveTo: '', formatInclusive() { return (this.inclusiveFrom || this.inclusiveTo) ? `From:${this.inclusiveFrom} - To:${this.inclusiveTo}` : '' }, submitForm() { this.$refs.leaveForm.submit(); } }">
 

    {{-- Leave Application Form Section --}}
    @if(!$this->lastApplication || in_array($this->lastApplication->status, ['Approved', 'Denied']))
        <div class="form-container">
            @if($employee)
                <div class="mb-4 bg-white dark:bg-[#282828] rounded shadow overflow-x-auto">
                    <h4 class="font-semibold mb-3 p-4 pb-0" style="color: #198f51;">LEAVE CARD SUMMARY</h4>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 40%; border-color: #ffffff;">TYPE OF LEAVE</th>
                                <th style="text-align: center !important; width: 12%; border-color: #ffffff;">Opening Balance</th>
                                <th style="text-align: center !important; width: 12%; border-color: #ffffff;">Credited/Earned</th>
                                <th style="text-align: center !important; width: 12%; border-color: #ffffff;">Total</th>
                                <th style="text-align: center !important; width: 12%; border-color: #ffffff;">Availed</th>
                                <th style="text-align: center !important; width: 12%; border-color: #ffffff;">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-medium">Vacation Leave</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['vacation']['opening'] }}</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['vacation']['earned'] }}</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['vacation']['total'] }}</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['vacation']['availed'] }}</td>
                                <td class="font-bold" style="text-align: center !important;">{{ $this->leaveTotals['vacation']['balance'] }}</td>
                            </tr>
                            <tr>
                                <td class="font-medium">Sick Leave</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['sick']['opening'] }}</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['sick']['earned'] }}</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['sick']['total'] }}</td>
                                <td style="text-align: center !important;">{{ $this->leaveTotals['sick']['availed'] }}</td>
                                <td class="font-bold" style="text-align: center !important;">{{ $this->leaveTotals['sick']['balance'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
            {{-- x-ref="leaveForm" --}}
            <form x-ref="leaveForm"  method="POST" action="{{ route('leave.user.submit') }}" >
                @method('POST')
                @csrf
                <x-primary-text-input name="lastname" label="Last Name" value="{{ old('lastname', $employee->lastname ?? '') }}" />
                <x-primary-text-input name="firstname" label="First Name" value="{{ old('firstname', $employee->firstname ?? '') }}" />
                <x-primary-text-input name="middlename" label="Middle Name" value="{{ old('middlename', $employee->middlename ?? '') }}" />
                <x-primary-text-input name="date_of_filing" type="date" label="Date" />
                <x-primary-text-input name="position" label="Position" value="{{ old('job_title', $employee->job_title ?? '') }}" />
                <x-primary-text-input name="salary" type="text" label="Salary" value="{{ old('salary', $employee->salary ?? '') }}" />
                <x-primary-text-input name="department" type="text" label="Office/Department" value="{{ old('department', $employee->department ?? '') }}" />
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label for="type_of_leave" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Leave Type :</label>
                    <select 
                    name="type_of_leave"
                    id="type_of_leave"
                    x-model="typeOfLeave" 
                    :required="true"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="">Select Type of Leave</option>
                        <option value="Vacation leave">Vacation Leave</option>
                        <option value="Mandatory/Forced leave">Mandatory/Forced Leave</option>
                        <option value="Sick leave">Sick Leave</option>
                        <option value="Maternity leave">Maternity Leave</option>
                        <option value="Paternity leave">Paternity Leave</option>
                        <option value="Special Privilege Leave">Special Privilege Leave</option>
                        <option value="Solo Parent leave">Solo Parent Leave</option>
                        <option value="Study leave">Study Leave</option>
                        <option value="10-Day VAWC leave">10-Day VAWC Leave</option>
                        <option value="Rehabilitation Privilege">Rehabilitation Privilege</option>
                        <option value="Special Leave Benefits for Women">Special Leave Benefits for Women</option>
                        <option value="Special Emergency(Calamity) Leave">Special Emergency(Calamity) Leave</option>
                        <option value="Adoption Leave">Adoption Leave</option>
                        <option value="others">Others</option>
                    </select>
                </div>

                <div
                x-show="typeOfLeave === 'others'" >
                    <x-primary-text-input 
                    name="others" 
                    type="text" 
                    label="Others please specify" 
                    :required="false" />
                </div>

                <div x-show="typeOfLeave === 'Vacation leave' || typeOfLeave === 'Special Privilege Leave'" >
                    <div 
                    class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label 
                    for="inCaseVacation"
                    class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Details of Leave</label>
                    <select
                    name="inCaseVacation"
                    id="inCaseVacation"
                    x-model="vacationDetails"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="">Select an option</option>
                        <option value="Within the Philippines">Within the Philippines </option>
                        <option value="Abroad">Abroad</option>
                    </select>
                    </div>
                </div>

                <div
                x-show="vacationDetails === 'Within the Philippines'" >
                    <x-primary-text-input 
                    name="withinPhilippines" 
                    type="text" 
                    label="Specify" 
                    :required="false" />
                </div>

                <div
                x-show="vacationDetails === 'Abroad'" >
                    <x-primary-text-input 
                    name="abroad" 
                    type="text" 
                    label="Specify" 
                    :required="false" />
                </div>

                <div x-show="typeOfLeave === 'Sick leave'" >
                    <div 
                    class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label 
                    for="inCaseSick"
                     class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Details of Leave</label>
                    <select 
                    
                    name="inCaseSick"
                    id="inCaseSick"
                    x-model="showSickDetails"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value=" ">Select an option</option>
                        <option value="In Hospital">In Hospital </option>
                        <option value="Out Patient">Out Patient</option>
                    </select>
                    </div>
                </div>

                <div
                x-show="showSickDetails === 'In Hospital'" >
                    <x-primary-text-input 
                    name="inHospital" 
                    type="text" 
                    label="Specify illness:" 
                    :required="false" />
                </div>

                <div
                x-show="showSickDetails === 'Out Patient'" >
                    <x-primary-text-input 
                    name="outPatient" 
                    type="text" 
                    label="Specify illness:" 
                    :required="false" />
                </div>

                <div x-show="typeOfLeave === 'Special Leave Benefits for Women'" >
                    <x-primary-text-input 
                    name="inCaseSpecialLeaveBenefits" 
                    type="text" 
                    label="(Specify Illness)" 
                    :required="false" />
                </div>

                <div
                x-show="typeOfLeave === 'Study leave'" >
                    <div 
                    class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label 
                    for="inCaseStudyLeave"
                     class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Details of Leave</label>
                    <select
                    :disabled="typeOfLeave !== 'Study leave'"
                    name="inCaseStudyLeave"
                    id="inCaseStudyLeave"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="Completion of Master's Degree">Completion of Master's Degree </option>
                        <option value="BAR/Board Examination Review ">BAR/Board Examination Review </option>
                        <option value="Terminal Leave ">Terminal Leave  </option>
                        <option value="Monetization of Leave Credits ">Monetization of Leave Credits </option>
                    </select>
                    </div>
                </div>

                 <div 
                    class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label 
                    for="commutation"
                     class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Commutation :</label>
                    <select 
                    name="commutation"
                    id="commutation"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="Not Requested">Not Requested </option>
                        <option value="Requested">Requested</option>
                    </select>
                    </div>

                <x-primary-text-input name="number_of_days" type="number" label="Number of Days" />

                <div class="mb-4">
                    <label for="inclusive_from" class="w-full font-bold custom-label mb-2 whitespace-nowrap">Inclusive Dates</label>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="inclusive_from" class="w-full sm:w-1/3 font-medium custom-label sm:pr-12 sm:text-left mb-1 sm:mb-0">From :</label>
                        <div class="flex-1">
                            <input id="inclusive_from" name="inclusive_from" type="date" x-model="inclusiveFrom" class="w-full border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" />
                        </div>
                    </div>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="inclusive_to" class="w-full sm:w-1/3 font-medium custom-label sm:pr-12 sm:text-left mb-1 sm:mb-0">To :</label>
                        <div class="flex-1">
                            <input id="inclusive_to" name="inclusive_to" type="date" x-model="inclusiveTo" class="w-full border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" />
                        </div>
                    </div>
                </div>

                <!-- hidden combined field submitted as inclusive_dates -->
                <input type="hidden" name="inclusive_dates" :value="formatInclusive()" />
                <div class="submit-container">
                    <button type="submit"  class="custom-submit-btn px-6 py-2 rounded-md font-medium">
                        Submit Leave Application
                    </button>
                </div>
            </form>
        </div>
    @endif




</div>