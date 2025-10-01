<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details div {
            margin-bottom: 10px;
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
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">SERVICE RECORD</div>
        <div class="details">
            <div><strong>Name:</strong> {{ $serviceRecord->name }}</div>
            <div><strong>Birth:</strong> {{ $serviceRecord->date_of_birth }} - {{ $serviceRecord->place_of_birth }}</div>
        </div>
        <p>This is to certify that the employee named herein-above actually rendered services in this office as shown in the service records below, each line of which is supported by the authorities concerned.</p>
        <table>
            <thead>
                <tr>
                    <th>Inclusive Dates</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Salary</th>
                    <th>Station/Place of Assignment</th>
                    <th>Date ABS</th>
                    <th>Cause</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $serviceRecord->inclusive_dates }}</td>
                    <td>{{ $serviceRecord->designation }}</td>
                    <td>{{ $serviceRecord->status }}</td>
                    <td>Php {{ number_format($serviceRecord->salary, 2) }}</td>
                    <td>{{ $serviceRecord->place_of_assignment }}</td>
                    <td>{{ $serviceRecord->date_abs }}</td>
                    <td>{{ $serviceRecord->cause }}</td>
                </tr>
            </tbody>
        </table>
        <p class="footer">Issued in compliance with Executive Order No.54 dated August 10, 1954, and in accordance with Circu dated August 10, 1954, of the system.</p>
        <p class="footer">CERTIFIED CORRECT: <br> {{ $serviceRecord->certified_by }}</p>
    </div>
</body>
</html>