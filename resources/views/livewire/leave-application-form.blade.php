<div x-data="{ typeOfLeave: '', showSickDetails: '', vacationDetails: '', showOverlay: false, submitForm() { this.$refs.leaveForm.submit(); } }">
 

    {{-- Leave Application Form Section --}}
    @if(!$this->lastApplication || in_array($this->lastApplication->status, ['Approved', 'Denied']))
        <div class="form-container">
            {{-- x-ref="leaveForm" --}}
            <form x-ref="leaveForm"  method="POST" action="{{ route('leave.user.submit') }}" >
                @method('POST')
                @csrf
                <x-primary-text-input name="lastname" label="Last Name" />
                <x-primary-text-input name="firstname" label="First Name"/>
                <x-primary-text-input name="middlename" label="Middle Name"/>
                <x-primary-text-input name="date_of_filing" type="date" label="Date"/>
                <x-primary-text-input name="position" label="Position" />
                <x-primary-text-input name="salary" type="number" label="Salary" /> 
                <x-primary-text-input name="department" type="text" label="Office/Department" /> 
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label for="type_of_leave" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Type of Leave</label>
                    <select 
                    name="type_of_leave"
                    id="type_of_leave"
                    x-model="typeOfLeave" 
                    :required="true"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="">Type of leave</option>
                        <option value="Vacation leave">Vacation leave</option>
                        <option value="Mandatory/Forced Leave">Mandatory/Forced Leave</option>
                        <option value="Sick Leave">Sick leave</option>
                        <option value="Maternity Leave">Maternity leave</option>
                        <option value="Paternity Leave">Paternity leave</option>
                        <option value="Special Privilege Leave">Special Privilege Leave</option>
                        <option value="Solo Parent Leave">Solo Parent leave</option>
                        <option value="Study Leave">Study leave</option>
                        <option value="10-Day VAWC Leave">10-Day VAWC leave</option>
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

                <div x-show="typeOfLeave === 'Sick Leave'" >
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
                x-show="typeOfLeave === 'Study Leave'" >
                    <div 
                    class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label 
                    for="inCaseStudyLeave"
                     class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Details of Leave</label>
                    <select
                    :disabled="typeOfLeave !== 'Study Leave'"
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
                     class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Commutation</label>
                    <select 
                    name="commutation"
                    id="commutation"
                    class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="Not Requested">Not Requested </option>
                        <option value="Requested">Requested</option>
                    </select>
                    </div>

                <x-primary-text-input name="number_of_days" type="number" label="Number of Days" />
                <x-primary-text-input name="inclusive_dates" type="text" label="Inclusive Dates" />
                <div class="submit-container">
                    <button type="submit"  class="custom-submit-btn px-6 py-2 rounded-md font-medium">
                        Submit Leave Application
                    </button>
                </div>
            </form>
        </div>
    @endif




</div>
