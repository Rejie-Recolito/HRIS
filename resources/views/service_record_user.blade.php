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
    // hasPending removed since request_status is no longer shown here
    $hasPending = false;
@endphp

<div class="py-12">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-green-600">Service Record</h1>
            </div>

            <div class="mb-4 border rounded-lg p-4" style="border-color:#2bb16b;">
                <div class="text-sm text-gray-700 dark:text-gray-200">
                    @if($serviceRecords->isNotEmpty())
                        @foreach($serviceRecords as $record)
                            <div class="mb-3">
                                <strong>
                                    {{ $record->date_of_service ? \Carbon\Carbon::parse($record->date_of_service)->format('F j, Y') : ($record->created_at ? $record->created_at->format('F j, Y') : '') }}
                                </strong>
                                <div>{{ $record->job_title ?? '—' }}</div>
                                <div>{{ $record->salary ? 'Salary Grade ' . $record->salary : '' }}</div>
                                <div>{{ $record->status ?? '' }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 text-gray-600">No records found.</div>
                    @endif
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">For an official service record document, that contains comprehensive personal data, detailed career progression, and other government-specific elements, with certification and verification; submit a request by clicking on the button below.</p>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">The request will be processed accordingly and can be claimed physically at the office.</p>

            <div x-data="serviceRecordRequest({ hasPending: {{ $hasPending ? 'true' : 'false' }}, requestUrl: {{ json_encode(route('service-records.request')) }}, csrfToken: {{ json_encode(csrf_token()) }} })" class="mt-6">
                <div id="sr-message" class="mb-3"></div>

                <button x-show="!hasPending" x-ref="openBtn" x-on:click="open()" type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded shadow">Request Complete Service Record</button>
                <button x-show="hasPending" disabled class="bg-gray-400 text-white font-semibold px-6 py-2 rounded shadow">Request Pending</button>

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