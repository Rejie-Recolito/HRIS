@php
$monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>DTR - {{ $empId }} - {{ $monthLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f4f4f4; }
        .header { margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DTR for Employee ID: {{ $empId }}</h2>
        <div>Month: {{ $monthLabel }}</div>
    </div>

    @if(count($dtrEntries))
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Employee Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dtrEntries as $entry)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($entry->occurred_at)->format('Y-m-d') }}</td>
                        <td>{{ $entry->time_in }}</td>
                        <td>{{ $entry->time_out }}</td>
                        <td>{{ $entry->emp_name ?? $entry->employee ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div>No DTR entries found for the provided Employee ID and month.</div>
    @endif
</body>
</html>