<div x-data="{ showOtherField: false, showOverlay: false, submitForm() { this.$refs.leaveForm.submit(); } }">

    

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

                <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                    <label for="type_of_leave" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Type of Leave</label>
                    <select name="type_of_leave" id="type_of_leave" @change="showOtherField = $event.target.value === 'others'" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                        <option value="Vacation leave">Vacation leave</option>
                        <option value="Mandatory/Forced Leave">Mandatory/Forced Leave</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Maternity Leave">Maternity Leave</option>
                        <option value="Paternity Leave">Paternity Leave</option>
                        <option value="Solo Parent Leave">Solo Parent Leave</option>
                        <option value="Study Leave">Study Leave</option>
                        <option value="10-Day VAWC Leave">10-Day VAWC Leave</option>
                        <option value="Rehabilitation Privilage">Rehabilitation Privilage</option>
                        <option value="Special Leave Benefits for Women">Special Leave Benefits for Women</option>
                        <option value="Special Emergency(Calamity) Leave">Special Emergency(Calamity) Leave</option>
                        <option value="Adoption Leave">Adoption Leave</option>
                        <option value="others">Others</option>
                    </select>
                </div>

                <div x-show="showOtherField" class="mt-4">
                    <x-primary-text-input name="others" label="Others" @required(false) />
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
