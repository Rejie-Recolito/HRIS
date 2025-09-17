<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Application Letter</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { font-size: 1.5em; font-weight: bold; margin-bottom: 20px; }
        .section { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">Leave Application Letter</div>
    <div class="section"><span class="label">Name:</span> {{ $leave->firstname }} {{ $leave->middlename }} {{ $leave->lastname }}</div>
    <div class="section"><span class="label">Date of Filing:</span> {{ $leave->date_of_filing }}</div>
    <div class="section"><span class="label">Position:</span> {{ $leave->position }}</div>
    <div class="section"><span class="label">Salary:</span> {{ $leave->salary }}</div>
    <div class="section"><span class="label">Type of Leave:</span> {{ $leave->type_of_leave }}</div>
    <div class="section"><span class="label">Others:</span> {{ $leave->others }}</div>
    <div class="section"><span class="label">Number of Working Days Applied for:</span> {{ $leave->number_of_days }}</div>
    <div class="section"><span class="label">Inclusive Dates:</span> {{ $leave->inclusive_dates }}</div>
    <br>
    <div class="section">I respectfully request approval for the above leave application.</div>
    <br>
    <div class="section">Signature: ___________________________</div>
</body>
</html>
