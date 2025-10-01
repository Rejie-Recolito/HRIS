<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\ServiceRecord;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $leaveApplications = LeaveApplication::count();
        $serviceRecordRequests = ServiceRecord::count();

        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'leaveApplications' => $leaveApplications,
            'serviceRecordRequests' => $serviceRecordRequests,
        ]);
    }
}
