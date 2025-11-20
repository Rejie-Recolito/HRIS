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
        
        <!-- Certified Documents Section -->
        @if($certifiedRequests->isNotEmpty())
            <div class="bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-[#198f51] mb-4">📄 Your Certified Service Records</h2>
                
                <div class="space-y-3">
                    @foreach($certifiedRequests as $request)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        Certified Service Record
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Certified on: {{ $request->certified_at ? $request->certified_at->format('F d, Y') : 'N/A' }}
                                    </p>
                                    @if($request->completed_at)
                                        <p class="text-xs text-green-600 dark:text-green-400">
                                            ✓ Downloaded on {{ $request->completed_at->format('F d, Y') }}
                                        </p>
                                    @else
                                        <p class="text-xs text-blue-600 dark:text-blue-400">
                                            🆕 New - Ready for download
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('service-records.download-certified', $request->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-[#198f51] text-white font-semibold rounded-md hover:bg-[#156b3f] transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <div class="bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-green-600">Service Record</h1>
            </div>

            <!-- Service Records Table -->
            <div class="mb-4 overflow-x-auto" style="max-width: 100%;">
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

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">For an official service record document, that contains comprehensive personal data, detailed career progression, and other government-specific elements, with certification and verification; submit a request by clicking on the button below.</p>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">The request will be processed accordingly and can be claimed physically at the office.</p>

            <div x-data="serviceRecordRequest({ hasPending: {{ $hasPending ? 'true' : 'false' }}, requestUrl: {{ json_encode(route('service-records.request')) }}, csrfToken: {{ json_encode(csrf_token()) }} })" class="mt-6">
                <div id="sr-message" class="mb-3"></div>

                @if($hasPending)
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 rounded-r">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Request Pending
                                </p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    Your service record request is being processed by HR.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$hasPending)
                    <button x-show="!hasPending" x-ref="openBtn" x-on:click="open()" type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded shadow">Request Complete Service Record</button>
                @else
                    <button disabled class="bg-gray-400 text-white font-semibold px-6 py-2 rounded shadow cursor-not-allowed">Request Pending</button>
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