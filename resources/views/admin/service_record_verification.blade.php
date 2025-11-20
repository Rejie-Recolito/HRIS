@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        SERVICE RECORD VERIFICATION & CERTIFICATION
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Progress Indicator -->
        <div class="mb-6 bg-white dark:bg-[#282828] rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Certification Progress</h3>
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $req->request_status === 'pending' ? 'bg-yellow-500' : 'bg-green-500' }} flex items-center justify-center text-white font-bold">
                            1
                        </div>
                        <div class="flex-1 h-1 {{ in_array($req->request_status, ['in_progress', 'verified', 'certified', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    </div>
                    <p class="mt-2 text-sm text-center dark:text-gray-300">Requested</p>
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $req->request_status === 'in_progress' ? 'bg-yellow-500' : (in_array($req->request_status, ['verified', 'certified', 'completed']) ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center text-white font-bold">
                            2
                        </div>
                        <div class="flex-1 h-1 {{ in_array($req->request_status, ['verified', 'certified', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    </div>
                    <p class="mt-2 text-sm text-center dark:text-gray-300">Verifying</p>
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $req->request_status === 'verified' ? 'bg-yellow-500' : (in_array($req->request_status, ['certified', 'completed']) ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center text-white font-bold">
                            3
                        </div>
                        <div class="flex-1 h-1 {{ in_array($req->request_status, ['certified', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    </div>
                    <p class="mt-2 text-sm text-center dark:text-gray-300">Verified</p>
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $req->request_status === 'certified' ? 'bg-yellow-500' : ($req->request_status === 'completed' ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center text-white font-bold">
                            4
                        </div>
                        <div class="flex-1 h-1 {{ $req->request_status === 'completed' ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    </div>
                    <p class="mt-2 text-sm text-center dark:text-gray-300">Certified</p>
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $req->request_status === 'completed' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white font-bold">
                            5
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-center dark:text-gray-300">Completed</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded dark:bg-green-900 dark:border-green-700 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded dark:bg-red-900 dark:border-red-700 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Employee Information (Read-Only) -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-[#198f51]">EMPLOYEE INFORMATION (Read-Only)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $employee ? trim(sprintf('%s, %s %s', $employee->lastname ?? '', $employee->firstname ?? '', $employee->middlename ?? '')) : $req->user->name }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $employee->date_of_birth ?? 'N/A' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Place of Birth</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $employee->place_of_birth ?? 'N/A' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $employee->designation ?? $employee->job_title ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Records Table (Read-Only) -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-[#198f51]">SERVICE RECORDS (Read-Only)</h3>
                
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="border-color: #ffffff;">From</th>
                                <th style="border-color: #ffffff;">To</th>
                                <th style="border-color: #ffffff;">Designation</th>
                                <th style="border-color: #ffffff;">Status</th>
                                <th style="border-color: #ffffff;">Station</th>
                                <th style="border-color: #ffffff;">Monthly Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceRecords as $record)
                                <tr>
                                    <td>{{ $record->service_from ? \Carbon\Carbon::parse($record->service_from)->format('Y-m-d') : '' }}</td>
                                    <td>{{ $record->service_to ? \Carbon\Carbon::parse($record->service_to)->format('Y-m-d') : '' }}</td>
                                    <td>{{ $record->appointment_designation ?? '' }}</td>
                                    <td>{{ $record->appointment_status ?? '' }}</td>
                                    <td>{{ $record->station ?? '' }}</td>
                                    <td>{{ $record->appointment_monthly_base_pay ? number_format($record->appointment_monthly_base_pay, 2) : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500">No service records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($serviceRecords->isEmpty())
                    <div class="mt-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-200">
                        <p class="font-semibold">⚠️ Warning: No service records found</p>
                        <p class="text-sm mt-1">Employee has no service record entries. You may need to add them in the <a href="{{ route('service-records.edit', $req->serviceRecord->id) }}" class="underline">employee service record page</a> before generating the document.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-[#198f51]">ACTIONS</h3>

                @if($req->request_status === 'in_progress')
                    <form method="POST" action="{{ route('service-records.mark-verified', $req->id) }}" class="mb-4">
                        @csrf
                        <div class="mb-4">
                            <label for="verification_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Verification Notes (Optional)
                            </label>
                            <textarea 
                                name="verification_notes" 
                                id="verification_notes" 
                                rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                                placeholder="Add any notes about data verification..."
                            >{{ old('verification_notes', $req->verification_notes) }}</textarea>
                        </div>
                        
                        <button type="submit" class="bg-[#198f51] text-white px-6 py-3 rounded-md hover:bg-[#156b3f] transition-colors font-semibold">
                            ✓ Mark Data as Verified
                        </button>
                    </form>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Please review all employee information and service records for accuracy before marking as verified.
                        If corrections are needed, <a href="{{ route('service-records.edit', $req->serviceRecord->id) }}" class="text-[#198f51] underline">edit the service record</a> first.
                    </p>
                @endif

                @if($req->request_status === 'verified')
                    <form method="POST" action="{{ route('service-records.generate-document', $req->id) }}">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors font-semibold">
                            📄 Generate PDF Document
                        </button>
                    </form>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Generate the official service record document in PDF format.
                    </p>
                @endif

                @if($req->generated_pdf_path && file_exists(storage_path('app/public/' . $req->generated_pdf_path)))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded dark:bg-green-900 dark:border-green-700">
                        <p class="text-green-700 dark:text-green-200 font-semibold">✓ Document Generated Successfully</p>
                        <a href="{{ asset('storage/' . $req->generated_pdf_path) }}" target="_blank" class="text-blue-600 dark:text-blue-400 underline text-sm mt-1 inline-block">
                            Preview PDF Document
                        </a>
                    </div>

                    @if($req->request_status === 'verified' && !$req->certified_at)
                        <form method="POST" action="{{ route('service-records.certify', $req->id) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition-colors font-semibold">
                                ✓ Certify Document & Notify Employee
                            </button>
                        </form>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Review the generated PDF. If correct, certify it to release to the employee.
                        </p>
                    @endif
                @endif

                @if($req->certified_at)
                    <div class="p-4 bg-green-100 border border-green-400 rounded dark:bg-green-900 dark:border-green-700">
                        <p class="text-green-700 dark:text-green-200 font-semibold">✓ Document Certified</p>
                        <p class="text-sm text-green-600 dark:text-green-300 mt-1">
                            Certified by: {{ $req->certifiedBy->name ?? 'Unknown' }} on {{ $req->certified_at->format('F d, Y h:i A') }}
                        </p>
                        <p class="text-sm text-green-600 dark:text-green-300">
                            Employee has been notified and can download the document.
                        </p>
                    </div>
                @endif

                @if($req->completed_at)
                    <div class="mt-4 p-4 bg-blue-100 border border-blue-400 rounded dark:bg-blue-900 dark:border-blue-700">
                        <p class="text-blue-700 dark:text-blue-200 font-semibold">✓ Request Completed</p>
                        <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">
                            Employee downloaded the document on {{ $req->completed_at->format('F d, Y h:i A') }}
                        </p>
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('service-record-requests.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        ← Back to Requests
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
