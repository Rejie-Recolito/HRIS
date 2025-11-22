@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-sm sm:text-xl text-white dark:text-white leading-tight flex items-center whitespace-nowrap">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
        </svg>
        {{ __('SERVICE RECORD') }}
    </h2>
@endsection

@section('content')

@php
    // Provided by controller; fallbacks if not present
    $serviceRecords = $serviceRecords ?? collect();
    $certifiedRequests = $certifiedRequests ?? collect();
    $hasPending = $hasPending ?? false;
@endphp

<div class="py-12">
    <div class="w-[95%] mx-auto px-4">
        
        
        <div class="bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-[#198f51]">Service Record</h1>
            </div>

            <!-- Service Records Table -->
            <div class="mb-7 overflow-x-auto" style="max-width: 100%;">
                <table class="admin-table" style="min-width: 1700px;">
                    <thead>
                        <tr>
                            <th colspan="2" style="border-color: #ffffff; text-align: center;">SERVICE<br>(Inclusive Dates)</th>
                            <th colspan="3" style="border-color: #ffffff; text-align: center;">RECORD OF APPOINTMENT</th>
                            <th style="border-color: #ffffff; text-align: center;">OFFICE ENTITY/DIV</th>
                            <th style="border-color: #ffffff; text-align: center;">LEAVE OF ABSENCE</th>
                            <th colspan="2" style="border-color: #ffffff; text-align: center;">SEPARATION</th>
                        </tr>
                        <tr>
                            <th style="border-color: #ffffff; min-width: 120px;">From</th>
                            <th style="border-color: #ffffff; min-width: 120px;">To</th>
                            <th style="border-color: #ffffff; min-width: 250px;">Designation</th>
                            <th style="border-color: #ffffff; min-width: 120px;">Status</th>
                            <th style="border-color: #ffffff; min-width: 120px;">Salary</th>
                                <th style="border-color: #ffffff; min-width: 200px;">Station/Place</th>
                                <th style="border-color: #ffffff; min-width: 150px;">w/o Pay</th>
                                <th style="border-color: #ffffff; min-width: 120px;">Date</th>
                            <th style="border-color: #ffffff; min-width: 150px;">Cause</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRecords as $record)
                            <tr>
                                <td>{{ $record->service_from ? $record->service_from->format('Y-m-d') : '' }}</td>
                                <td>{{ $record->service_to ? $record->service_to->format('Y-m-d') : '' }}</td>
                                <td>{{ $record->appointment_designation }}</td>
                                <td>{{ $record->appointment_status }}</td>
                                <td class="text-right">{{ number_format($record->appointment_salary, 2) }}</td>
                                <td>{{ $record->station_place }}</td>
                                <td>{{ $record->leave_of_absence }}</td>
                                <td class="text-xs">{{ $record->separation_date ? $record->separation_date->format('Y-m-d') : '' }}</td>
                                <td>{{ $record->separation_cause }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    No service records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-lg mb-4">For an official service record document, that contains personal data and with certification and verification for whatever purposes; submit a request by clicking on the button below.</p>
            <p class="text-lg mb-4">The request will be processed accordingly and can be claimed physically at the office.</p>
    
            <div x-data="serviceRecordRequest({ hasPending: {{ $hasPending ? 'true' : 'false' }}, requestUrl: {{ json_encode(route('service-records.request')) }}, csrfToken: {{ json_encode(csrf_token()) }} })" class="mt-6">
                <div id="sr-message" class="mb-3"></div>


                {{-- Ready for Claim Notification --}}
                @php
                    $readyForClaim = $certifiedRequests->firstWhere('request_status', 'ready_for_claim');
                @endphp
                @if($readyForClaim)
                    <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-800 rounded">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <strong>Your requested Certified True Copy of Service Record is ready to be claimed physically at the MHRMO.</strong>
                            </div>
                            <form method="POST" action="{{ route('service-records.mark-claimed', $readyForClaim->id) }}" class="mt-3 md:mt-0 md:ml-6">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">Mark as Claimed</button>
                            </form>
                        </div>
                    </div>
                @else
                    @if($hasPending)
                        <div class="mb-4 p-4 bg-[#e3f9ec] dark:bg-[#486da90f] border-l-4 border-[#198f51] rounded-r">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-[#198f51]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-md mt-1">
                                        Your service record request is currently being processed by HR.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <button disabled class="bg-gray-400 text-white font-semibold px-6 py-2 rounded shadow cursor-not-allowed">Request Pending</button>
                    @else
                        <button x-show="!hasPending" x-ref="openBtn" x-on:click="open()" type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded shadow">Request Certified True Copy</button>
                    @endif
                @endif

                <!-- Modal -->
                <div x-show="showModal" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-11/12 max-w-md">
                        <h3 class="text-lg font-semibold mb-3">Confirm Request</h3>
                        <p class="mb-4">Are you sure you want to request a Service Record? The administrator will complete the details for you.</p>
                        <div class="flex justify-end space-x-2">
                            <button type="button" x-on:click="close()" class="px-3 py-1 rounded border">Cancel</button>
                            <button type="button" x-on:click="submit()" x-bind:disabled="submitting" class="px-3 py-1 rounded bg-green-600 text-white" x-text="submitting ? 'Submitting...' : 'Confirm'"></button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script>
    function serviceRecordRequest(opts) {
        return {
            hasPending: opts.hasPending === true || opts.hasPending === 'true',
            requestUrl: opts.requestUrl,
            csrfToken: opts.csrfToken,
            showModal: false,
            submitting: false,
            open() {
                this.showModal = true;
            },
            close() {
                this.showModal = false;
            },
            async submit() {
                this.submitting = true;
                const msg = document.getElementById('sr-message');
                if (msg) msg.innerHTML = 'Submitting request...';
                try {
                    const res = await fetch(this.requestUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ _token: this.csrfToken })
                    });

                    if (!res.ok) {
                        const text = await res.text();
                        if (msg) msg.innerHTML = '<div class="text-red-600">Request failed: ' + res.status + ' ' + res.statusText + '</div>';
                        console.error('Service record request failed:', res.status, text);
                        this.submitting = false;
                        return;
                    }

                    const json = await res.json();
                    if (msg) msg.innerHTML = '<div class="text-green-600">Request submitted. The admin will complete the form.</div>';
                    // Reload to show pending state (simplest UX)
                    setTimeout(() => location.reload(), 900);
                } catch (err) {
                    console.error(err);
                    if (msg) msg.innerHTML = '<div class="text-red-600">Network error while submitting request.</div>';
                    this.submitting = false;
                }
            }
        }
    }
</script>

@endsection