<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details div {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Service Record</div>
        <div class="details">
            <div>Name: {{ $serviceRecord->name }}</div>
            <div>Age: {{ $serviceRecord->age }}</div>
            <div>Salary: Php {{ number_format($serviceRecord->salary, 2) }}</div>
            <div>Date of Birth: {{ $serviceRecord->date_of_birth }}</div>
            <div>Job Title: {{ $serviceRecord->job_title }}</div>
            <div>Place of Birth: {{ $serviceRecord->place_of_birth }}</div>
            <div>Office: {{ $serviceRecord->office }}</div>
            <div>Status: {{ $serviceRecord->status }}</div>
            <div>Date of Service: {{ $serviceRecord->date_of_service }}</div>
            <div>Place of Assignment: {{ $serviceRecord->place_of_assignment }}</div>
        </div>
    </div>
</body>
</html>