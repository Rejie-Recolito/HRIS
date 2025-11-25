@extends('layouts.app')
@section('content')
    <div class="dashboard-main-row flex flex-col lg:flex-row gap-6">
        <div class="dashboard-left-col w-full lg:w-2/3 pr-0 lg:pr-4">
            <!-- Dashboard Widgets Row -->
            <div class="flex gap-6 mt-5 mb-10 ml-3 w-full">
                <div class="flex-1 bg-[#198f51] dark:bg-[#198f51] rounded-xl shadow p-3 flex flex-col items-start justify-between min-w-[180px]">
                    <div class="flex items-center">
                        <svg class="w-10 h-10 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-3xl font-bold text-white ml-2">{{ $totalEmployees }}</span>
                    </div>
                    <span class="text-sm font-semibold text-white ml-0">TOTAL EMPLOYEES</span>
                </div>
                <div class="flex-1 bg-[#198f51] dark:bg-[#198f51] rounded-xl shadow p-3 flex flex-col items-start justify-between min-w-[180px]">
                    <div class="flex items-center">
                        <img src="{{ asset('images/leave-icon.svg') }}" class="w-10 h-10 mr-2 filter invert brightness-0" alt="Leave Icon">
                        <span class="text-3xl font-bold text-white ml-2">{{ $leaveApplications }}</span>
                    </div>
                    <span class="text-sm font-semibold text-white ml-0">LEAVE APPLICATIONS</span>
                </div>
                <div class="flex-1 bg-[#198f51] dark:bg-[#198f51] rounded-xl shadow p-3 flex flex-col items-start justify-between min-w-[180px]">
                    <div class="flex items-center">
                        <svg class="w-10 h-10 mr-2 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        <span class="text-3xl font-bold text-white ml-2">{{ $serviceRecordRequests }}</span>
                    </div>
                    <span class="text-sm font-semibold text-white ml-0">SR REQUESTS</span>
                </div>
                <div class="flex-1 bg-[#198f51] dark:bg-[#198f51] rounded-xl shadow p-3 flex flex-col items-start justify-between min-w-[180px]">
                    <div class="flex items-center">
                        <svg class="w-10 h-10 mr-2 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 1114 0H3z" />
                        </svg>
                        <span class="text-3xl font-bold text-white ml-2">{{ $accountsNeedingApproval }}</span>
                    </div>
                    <span class="text-sm font-semibold text-white ml-0">ACCOUNT APPROVAL</span>
                </div>
            </div>
            <!-- Statistical Summary Graph Section -->
            <div class="bg-white dark:bg-[#282828] border border-[#198f51] dark:border-[#2bb16b] rounded-xl shadow p-4 mb-8 ml-3 w-full" style="border-width: 2px;">
                <h3 class="font-semibold mb-2 text-lg text-[#198f51]">TRANSACTIONS SUMMARY</h3>
                <canvas id="statSummaryChart" height="80"></canvas>
            </div>
            <!-- Filter Section -->
            <div class="flex flex-wrap gap-4 mb-6 ml-5 w-full">
                <form id="trendFilterForm" class="flex flex-wrap gap-4 w-full">
                    <div>
                        <label for="filter_user_name" class="block text-sm font-medium text-gray-700 dark:text-white">Employee Name</label>
                        <div class="relative">
                            <input type="text" id="filter_user_name" name="user_name" autocomplete="off" class="mt-1 block w-48 rounded-lg border-gray-300" placeholder="Type employee name...">
                            <input type="hidden" id="filter_user_id" name="user_id" value="">
                            <ul id="user_name_suggestions" class="absolute z-10 bg-white border border-gray-300 rounded w-48 mt-1 hidden max-h-40 overflow-y-auto"></ul>
                        </div>
                    </div>
                    <div>
                        <label for="filter_department" class="block text-sm font-medium text-gray-700 dark:text-white">Department</label>
                        <select id="filter_department" name="department" class="mt-1 block w-48 rounded-lg border-gray-300">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_report_type" class="block text-sm font-medium text-gray-700 dark:text-white">Report Type</label>
                        <select id="filter_report_type" name="report_type" class="mt-1 block w-48 rounded border-gray-300">
                            <option value="">All</option>
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <!-- Chart.js Trends Section -->
            <div class="bg-white dark:bg-[#282828] border border-[#198f51] dark:border-[#2bb16b] rounded-xl ml-3 shadow p-4 mb-10 w-full" style="border-width: 2px;">
                <h3 class="font-semibold text-lg mb-2 text-[#198f51]">TRENDS (Last 12 Months)</h3>
                    <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>

            <!-- Recent Activity Feed -->
        <div class="dashboard-right-col w-full mr-3 lg:w-1/3">
            <div class="bg-white dark:bg-[#282828] border border-[#198f51] dark:border-[#2bb16b] rounded-xl mt-5 shadow p-4 w-full sticky top-8 self-start">
                <h3 class="font-semibold mb-4 text-lg text-[#198f51]">RECENT ACTIVITY FEED</h3>
                @if($recentActivities->count())
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentActivities as $activity)
                            <li class="py-3 hover:bg-gray-50 dark:hover:bg-[#222c22] transition">
                                <div class="text-xs text-gray-400 dark:text-gray-300 mb-1">
                                    {{ $activity['timestamp']->format('M d, Y h:i A') }}
                                </div>
                                <a href="{{ $activity['link'] }}" class="flex items-center group text-gray-900 dark:text-white">
                                    <span class="inline-block w-2 h-2 rounded-full mr-3"
                                        style="background: {{ $activity['type'] === 'Leave Application' ? '#10b981' : ($activity['type'] === 'Employee' ? '#eab308' : '#2563eb') }};"></span>
                                    <span class="font-medium mr-2 text-xs">{{ $activity['type'] }}</span>
                                    <span class="mr-2 text-xs">{{ $activity['user'] }}</span>
                                    <span class="text-xs mr-2">({{ $activity['status'] }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-gray-500 dark:text-gray-300">No recent activity.</div>
                @endif
            </div>
        </div>
    </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Statistical Summary Bar Graph (all periods grouped)
                const statSummaryCtx = document.getElementById('statSummaryChart').getContext('2d');
                const statLabels = ['Completed Leave Applications', 'Completed Service Records'];
                const statDatasets = [
                    {
                        label: 'Weekly',
                        data: [
                            {{ $leaveWeekly ?? 0 }},
                            {{ $serviceWeekly ?? 0 }}
                        ],
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Monthly',
                        data: [
                            {{ $leaveMonthlyCount ?? 0 }},
                            {{ $serviceMonthlyCount ?? 0 }}
                        ],
                        backgroundColor: 'rgba(16,185,129,0.7)',
                        borderColor: 'rgba(16,185,129,1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Quarterly',
                        data: [
                            {{ $leaveQuarterly ?? 0 }},
                            {{ $serviceQuarterly ?? 0 }}
                        ],
                        backgroundColor: 'rgba(234,179,8,0.7)',
                        borderColor: 'rgba(234,179,8,1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Annual',
                        data: [
                            {{ $leaveAnnually ?? 0 }},
                            {{ $serviceAnnually ?? 0 }}
                        ],
                        backgroundColor: 'rgba(37,99,235,0.7)',
                        borderColor: 'rgba(37,99,235,1)',
                        borderWidth: 1
                    }
                ];
                let statSummaryChart = new Chart(statSummaryCtx, {
                    type: 'bar',
                    data: {
                        labels: statLabels,
                        datasets: statDatasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                const months = {!! json_encode($months) !!};
                const leaveMonthly = {!! json_encode($leaveMonthly) !!};
                const serviceRecordMonthly = {!! json_encode($serviceRecordMonthly) !!};
                console.log('Chart Data:', { months, leaveMonthly, serviceRecordMonthly });

                // Users list is provided by the server; we'll use it for client-side suggestions
                const users = {!! json_encode($users->map(function($u){ return ['id' => $u->id, 'name' => $u->name]; })) !!};

                const ctx = document.getElementById('trendChart').getContext('2d');
                let trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Leave Applications',
                                data: leaveMonthly,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16,185,129,0.1)',
                                fill: true,
                                hidden: false,
                            },
                            {
                                label: 'Service Record Requests',
                                data: serviceRecordMonthly,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37,99,235,0.1)',
                                fill: true,
                                hidden: false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                function updateChart(data, type) {
                    trendChart.data.datasets[0].data = data.leaveMonthly;
                    trendChart.data.datasets[1].data = data.serviceRecordMonthly;
                    // If type is empty ("All"), show both datasets
                    if (!type) {
                        trendChart.data.datasets[0].hidden = false;
                        trendChart.data.datasets[1].hidden = false;
                    } else {
                        trendChart.data.datasets[0].hidden = type !== 'leave';
                        trendChart.data.datasets[1].hidden = type !== 'service_record';
                    }
                    trendChart.update();
                }

                // Debounce function to limit calls while typing
                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }

                function fetchTrends() {
                    const userId = document.getElementById('filter_user_id').value;
                    const department = document.getElementById('filter_department').value;
                    const reportType = document.getElementById('filter_report_type').value;
                    const params = new URLSearchParams();
                    if (userId) params.append('user_id', userId);
                    if (department) params.append('department', department);
                    if (reportType) params.append('report_type', reportType);

                    fetch(`/admin/dashboard/trends?${params.toString()}`)
                        .then(res => res.json())
                        .then(data => {
                            updateChart(data, reportType);
                        });
                }

                const userNameInput = document.getElementById('filter_user_name');
                const userIdInput = document.getElementById('filter_user_id');
                const suggestionsBox = document.getElementById('user_name_suggestions');

                // Suggestion handling using the server-provided `users` array
                userNameInput.addEventListener('input', debounce(function() {
                    const query = userNameInput.value.trim();
                    // clear selected user id when typing changes
                    userIdInput.value = '';
                    if (query.length < 2) {
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.classList.add('hidden');
                        fetchTrends();
                        return;
                    }

                    const q = query.toLowerCase();
                    const matches = users.filter(u => u.name && u.name.toLowerCase().includes(q)).slice(0, 10);
                    if (matches.length > 0) {
                        suggestionsBox.innerHTML = matches.map(u => `<li data-id="${u.id}" class='px-3 py-1 hover:bg-gray-100 cursor-pointer'>${u.name}</li>`).join('');
                        suggestionsBox.classList.remove('hidden');
                    } else {
                        suggestionsBox.innerHTML = '<li class="px-3 py-1 text-gray-400">No matches found</li>';
                        suggestionsBox.classList.remove('hidden');
                    }

                    // also fetch trends for broader queries (without user_id)
                    fetchTrends();
                }, 300));

                suggestionsBox.addEventListener('mousedown', function(e) {
                    if (e.target.tagName === 'LI' && !e.target.classList.contains('text-gray-400')) {
                        const selectedId = e.target.dataset.id;
                        const selectedName = e.target.textContent;
                        userNameInput.value = selectedName;
                        userIdInput.value = selectedId;
                        suggestionsBox.classList.add('hidden');
                        fetchTrends();
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!userNameInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.classList.add('hidden');
                    }
                });

                document.getElementById('filter_department').addEventListener('change', fetchTrends);
                document.getElementById('filter_report_type').addEventListener('change', fetchTrends);
            });
            </script>

@endsection
