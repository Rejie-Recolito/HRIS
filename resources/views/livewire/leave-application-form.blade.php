<div x-data="{  showOverlay: false, submitForm() { this.$refs.leaveForm.submit(); } }">

    {{-- Leave Application Status Section --}}
    @if($this->lastApplication && in_array($this->lastApplication->status, ['Under Review', 'Submitted']))
        <div class="status-container mb-6">
            <div class="mb-4 text-blue-600">
                Your leave application status: {{ $this->lastApplication->status }}
            </div>
        </div>
    @endif


    {{-- Leave Application Form Section --}}
    @if(!$this->lastApplication || in_array($this->lastApplication->status, ['Approved', 'Denied']))
        <div class="form-container">
            <form x-ref="leaveForm" method="POST" action="{{ route('leave.user.submit') }}" @submit.prevent="showOverlay = true">
                @csrf
                <x-primary-text-input name="lastname" label="Lastname" />
                <x-primary-text-input name="firstname" label="Firstname"/>
                <x-primary-text-input name="middlename" label="Middlename"/>
                <x-primary-text-input name="date_of_filing" type="date" label="Date"/>
                <x-primary-text-input name="position" label="Position" />
                <x-primary-text-input name="salary" type="number" label="Salary" /> 

                <x-primary-text-input 
                    name="type_of_leave" 
                    type="select" 
                    :options="[
                        'Vacation leave',
                        'Mandatory/Forced Leave',
                        'Sick Leave',
                        'Maternity Leave',
                        'Paternity Leave',
                        'Solo Parent Leave',
                        'Study Leave',
                        '10-Day VAWC Leave',
                        'Rehabilitation Privilage',
                        'Special Leave Benefits for Women',
                        'Special Emergency(Calamity) Leave',
                        'Adoption Leave',
                        'others'
                    ]"
                    label="Type of Leave"
                    
                />

                

                <x-primary-text-input name="number_of_days" type="number" label="Number of Days" />
                <x-primary-text-input name="inclusive_dates" type="text" label="Inclusive Dates" />
                <div class="submit-container">
                    <button type="submit" class="custom-submit-btn px-6 py-2 rounded-md font-medium">
                        Submit Leave Application
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Leave Application Status Section --}}
    @if($this->lastApplication && in_array($this->lastApplication->status, ['Under Review', 'Submitted']))
        <div class="status-container mb-6">
            <div class="mb-4 text-blue-600">
                Your leave application status: {{ $this->lastApplication->status }}
            </div>
        </div>
    @endif

    <div x-show="showOverlay" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg text-center">
            <h2 class="text-lg font-semibold text-green-600 mb-4">Leave application submitted</h2>
            <button @click="submitForm" class="bg-green-600 text-white px-4 py-2 rounded">OK</button>
        </div>
    </div>

</div>
