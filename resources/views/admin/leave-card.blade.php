@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        {{ __('EMPLOYEE\'S LEAVE CARD') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="w-[90%] mx-auto sm:px-6 lg:px-8">
            <div class="card-bg p-6">
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

                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Employee : {{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }}</p>
                    <p class="text-m text-gray-500 dark:text-gray-400">Department: {{ $employee->department }} | Position: {{ $employee->job_title }}</p>
                </div>

                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="md:w-[70%] bg-white dark:bg-[#282828] rounded shadow overflow-x-auto">
                        <h4 class="font-semibold mb-3 p-4 pb-0" style="color: #198f51;">LEAVE CREDITS SUMMARY</h4>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%; border-color: #ffffff;">TYPE OF LEAVE</th>
                                    <th style="text-align: center !important; width: 14%; border-color: #ffffff;">Year-Start Balance</th>
                                    <th style="text-align: center !important; width: 14%; border-color: #ffffff;">Credited/Earned</th>
                                    <th style="text-align: center !important; width: 14%; border-color: #ffffff;">Total</th>
                                    <th style="text-align: center !important; width: 14%; border-color: #ffffff;">Availed</th>
                                    <th style="text-align: center !important; width: 14%; border-color: #ffffff;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-medium">Vacation Leave</td>
                                    <td style="text-align: center !important;">{{ $openingBalance['vacation'] }}</td>
                                    <td style="text-align: center !important;">{{ $earnedCredits['vacation'] }}</td>
                                    <td style="text-align: center !important;">{{ $totalEarned['vacation'] }}</td>
                                    <td style="text-align: center !important;">{{ $availed['vacation'] }}</td>
                                    <td class="font-bold" style="text-align: center !important;">{{ $balance['vacation'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Sick Leave</td>
                                    <td style="text-align: center !important;">{{ $openingBalance['sick'] }}</td>
                                    <td style="text-align: center !important;">{{ $earnedCredits['sick'] }}</td>
                                    <td style="text-align: center !important;">{{ $totalEarned['sick'] }}</td>
                                    <td style="text-align: center !important;">{{ $availed['sick'] }}</td>
                                    <td class="font-bold" style="text-align: center !important;">{{ $balance['sick'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="md:w-[30%] p-4 bg-white dark:bg-[#282828] rounded shadow">
                        <h4 class="font-semibold mb-3 p-0 pb-0" style="color: #198f51;">ADD MONTHLY CREDIT</h4>
                        <form method="POST" action="{{ route('employees.leave_card.store', $employee->id) }}">
                            @csrf
                            <div class="mb-5">
                                <label for="type" class="block text-sm text-gray-900 dark:text-gray-200">Type of Leave</label>
                                <select name="type" id="type" class="w-full border rounded-lg px-2 py-1 bg-white dark:bg-[#282828] dark:text-white" style="border-color: #198f51;">
                                    <option value="vacation">Vacation</option>
                                    <option value="sick">Sick</option>
                                </select>
                            </div>
                            <div class="mb-2 flex items-center gap-2">
                                <label for="amount" class="text-sm whitespace-nowrap text-gray-900 dark:text-gray-200">Credit:</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="flex-1 border rounded-lg px-2 py-1 bg-white dark:bg-[#282828] dark:text-white" style="border-color: #198f51;" required>
                            </div>
        
                            <div class="flex justify-center">
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1 bg-[#198f51] hover:bg-[#166534] text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#166534] whitespace-nowrap">Add</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- History Section -->
                <div class="mt-6">
                    <!-- Vacation Leave History -->
                    <div class="bg-white dark:bg-[#282828] p-4 rounded shadow mb-4">
                        <h4 class="text-lg font-semibold mb-4" style="color: #198f51;">VACATION LEAVE HISTORY</h4>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="border-color: #ffffff;">Date</th>
                                    <th style="border-color: #ffffff;">Particulars</th>
                                    <th style="text-align: center !important; border-color: #ffffff;">Days Credited (+)</th>
                                    <th style="text-align: center !important; border-color: #ffffff;">Days Availed (-)</th>
                                    <th style="text-align: center !important; border-color: #ffffff;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                <tr>
                                    <td>-</td>
                                    <td class="font-semibold">Opening Balance</td>
                                    <td style="text-align: center !important;">-</td>
                                    <td style="text-align: center !important;">-</td>
                                    <td class="font-semibold" style="text-align: center !important;">{{ number_format($openingBalance['vacation'], 2) }}</td>
                                </tr>
                                @forelse($vacationHistory as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                                        <td>{{ $item['particulars'] }}</td>
                                        <td style="text-align: center !important;">
                                            @if($item['credited'] > 0)
                                                <span class="text-green-600 dark:text-green-400">+{{ number_format($item['credited'], 2) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="text-align: center !important;">
                                            @if($item['availed'] > 0)
                                                <span class="text-red-600 dark:text-red-400">-{{ number_format($item['availed'], 2) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="font-semibold" style="text-align: center !important;">{{ number_format($item['balance'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center !important;" class="text-gray-500 dark:text-gray-400">No transaction history yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Sick Leave History -->
                    <div class="bg-white dark:bg-[#282828] p-4 rounded shadow">
                        <h4 class="text-lg font-semibold mb-4" style="color: #198f51;">SICK LEAVE HISTORY</h4>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="border-color: #ffffff;">Date</th>
                                    <th style="border-color: #ffffff;">Particulars</th>
                                    <th style="text-align: center !important; border-color: #ffffff;">Days Credited (+)</th>
                                    <th style="text-align: center !important; border-color: #ffffff;">Days Availed (-)</th>
                                    <th style="text-align: center !important; border-color: #ffffff;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                <tr>
                                    <td>-</td>
                                    <td class="font-semibold">Opening Balance</td>
                                    <td class="text-center" style="text-align: center !important;">-</td>
                                    <td class="text-center" style="text-align: center !important;">-</td>
                                    <td class="font-semibold text-center" style="text-align: center !important;">{{ number_format($openingBalance['sick'], 2) }}</td>
                                </tr>
                                @forelse($sickHistory as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                                        <td>{{ $item['particulars'] }}</td>
                                        <td class="text-center" style="text-align: center !important;">
                                            @if($item['credited'] > 0)
                                                <span class="text-green-600 dark:text-green-400">+{{ number_format($item['credited'], 2) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center" style="text-align: center !important;">
                                            @if($item['availed'] > 0)
                                                <span class="text-red-600 dark:text-red-400">-{{ number_format($item['availed'], 2) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="font-semibold text-center" style="text-align: center !important;">{{ number_format($item['balance'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center !important;" class="text-gray-500 dark:text-gray-400">No transaction history yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
