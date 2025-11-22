@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/css/admin-dashboard-widgets.css">
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Admin Dashboard
    </h1>

    <!-- Dashboard Widgets -->
    <div class="widget-row">
        <div class="widget-square">
            <div style="display: flex; align-items: center;">
                <svg class="widget-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m6-8a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
                <span class="widget-title">Total Employees</span>
            </div>
            <span class="widget-value">{{ $totalEmployees }}</span>
        </div>
        <div class="widget-square">
            <div style="display: flex; align-items: center;">
                <svg class="widget-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-6 4h6m-6 4h6m-6 4h6m-6 4h6" />
                </svg>
                <span class="widget-title">Leave Applications</span>
            </div>
            <span class="widget-value">{{ $leaveApplications }}</span>
        </div>
        <div class="widget-square">
            <div style="display: flex; align-items: center;">
                <svg class="widget-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="widget-title">Service Record Requests</span>
            </div>
            <span class="widget-value">{{ $serviceRecordRequests }}</span>
        </div>
    </div>

    <!-- Statistical Summary Graph Section -->
    <div class="bg-white rounded shadow p-4 mb-8 mt-10">
        <h3 class="font-semibold mb-2">Statistical Summary (Bar Graph)</h3>
        <canvas id="statSummaryChart" height="80"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statLabels = ['Weekly', 'Monthly', 'Quarterly', 'Annually'];
    const leaveStats = {!! json_encode([$leaveWeekly ?? 0, $leaveMonthlyCount ?? 0, $leaveQuarterly ?? 0, $leaveAnnually ?? 0]) !!};
    const serviceStats = {!! json_encode([$serviceWeekly ?? 0, $serviceMonthlyCount ?? 0, $serviceQuarterly ?? 0, $serviceAnnually ?? 0]) !!};
    const statCtx = document.getElementById('statSummaryChart').getContext('2d');
    new Chart(statCtx, {
        type: 'bar',
        data: {
            labels: statLabels,
            datasets: [
                {
                    label: 'Leave Applications',
                    data: leaveStats,
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1
                },
                {
                    label: 'Service Record Requests',
                    data: serviceStats,
                    backgroundColor: 'rgba(37,99,235,0.7)',
                    borderColor: '#2563eb',
                    borderWidth: 1
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
</script>

    <!-- Filter Section -->
    <div class="flex flex-wrap gap-4 mt-10 mb-6">
        <form id="trendFilterForm" class="flex flex-wrap gap-4 w-full">
            <div>
                <label for="filter_user_name" class="block text-sm font-medium text-gray-700">Employee Name</label>
                <input type="text" id="filter_user_name" name="user_name" class="mt-1 block w-48 rounded border-gray-300" placeholder="Type employee name...">
            </div>
            <div>
                <label for="filter_department" class="block text-sm font-medium text-gray-700">Department</label>
                <select id="filter_department" name="department" class="mt-1 block w-48 rounded border-gray-300">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                <select id="filter_report_type" name="report_type" class="mt-1 block w-48 rounded border-gray-300">
                    @foreach($reportTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Chart.js Trends Section -->
    <div class="bg-white rounded shadow p-4 mb-8 mt-8">
        <h3 class="font-semibold mb-2">Trends (Last 12 Months)</h3>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const months = @json($months);
        const leaveMonthly = @json($leaveMonthly);
        const serviceRecordMonthly = @json($serviceRecordMonthly);
        let currentType = document.getElementById('filter_report_type').value;

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
                        hidden: currentType !== 'leave',
                    },
                    {
                        label: 'Service Record Requests',
                        data: serviceRecordMonthly,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        fill: true,
                        hidden: currentType !== 'service_record',
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
            trendChart.data.datasets[0].hidden = type !== 'leave';
            trendChart.data.datasets[1].hidden = type !== 'service_record';
            trendChart.update();
        }

        // Debounce function to limit AJAX calls while typing
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function fetchTrends() {
            const userName = document.getElementById('filter_user_name').value;
            const department = document.getElementById('filter_department').value;
            const reportType = document.getElementById('filter_report_type').value;
            const params = new URLSearchParams({ user_name: userName, department, report_type: reportType });
            fetch(`/admin/dashboard/trends?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    updateChart(data, reportType);
                });
        }

        document.getElementById('filter_user_name').addEventListener('input', debounce(fetchTrends, 400));
        document.getElementById('filter_department').addEventListener('change', fetchTrends);
        document.getElementById('filter_report_type').addEventListener('change', fetchTrends);
    </script>
</div>
@endsection
