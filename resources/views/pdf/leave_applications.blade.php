<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Applications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Leave Applications</h1>
    @foreach ($leaveApplications as $leave)
        <h2>Application for Leave</h2>
        <table>
            <tr>
                <td>Office/Department:</td>
                <td>{{ $leave->office }}</td>
            </tr>
            <tr>
                <td>Name:</td>
                <td>{{ $leave->lastname }}, {{ $leave->firstname }} {{ $leave->middlename }}</td>
            </tr>
            <tr>
                <td>Date of Filing:</td>
                <td>{{ $leave->date_of_filing }}</td>
            </tr>
            <tr>
                <td>Position:</td>
                <td>{{ $leave->position }}</td>
            </tr>
            <tr>
                <td>Salary:</td>
                <td>{{ $leave->salary }}</td>
            </tr>
        </table>

        <h3>Details of Application</h3>
        <table>
            <tr>
                <td>Type of Leave:</td>
                <td>{{ $leave->type_of_leave }}</td>
            </tr>
            <tr>
                <td>Others:</td>
                <td>{{ $leave->others }}</td>
            </tr>
            <tr>
                <td>Number of Working Days Applied For:</td>
                <td>{{ $leave->number_of_days }}</td>
            </tr>
            <tr>
                <td>Inclusive Dates:</td>
                <td>{{ $leave->inclusive_dates }}</td>
            </tr>
        </table>

        <h3>Details of Action on Application</h3>
        <table>
            <tr>
                <td>Certification of Leave Credits:</td>
                <td>Total Earned: {{ $leave->leave_credits_total }}, Less This Application: {{ $leave->leave_credits_used }}</td>
            </tr>
            <tr>
                <td>Recommendation:</td>
                <td>{{ $leave->recommendation }}</td>
            </tr>
            <tr>
                <td>Approved For:</td>
                <td>{{ $leave->approved_for }}</td>
            </tr>
            <tr>
                <td>Disapproved Due To:</td>
                <td>{{ $leave->disapproved_reason }}</td>
            </tr>
        </table>
        <hr>
    @endforeach
</body>
</html>
