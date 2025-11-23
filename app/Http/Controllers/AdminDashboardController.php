<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\ServiceRecord;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function trends(Request $request)
    {
        $userId = $request->input('user_id');
        $department = $request->input('department');
        $reportType = $request->input('report_type', 'leave');

        $months = collect(range(0, 11))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->reverse()->values();

        $leaveMonthly = $months->map(function ($month) use ($userId, $department) {
            $query = \App\Models\LeaveApplication::where('status', 'Approved');
            $query->whereYear('approved_at', substr($month, 0, 4))
                  ->whereMonth('approved_at', substr($month, 5, 2));
            if ($userId) {
                $query->where('user_id', $userId);
            }
            if ($department) {
                $query->where('department', $department);
            }
            return $query->count();
        });

        $serviceRecordMonthly = $months->map(function ($month) use ($userId, $department) {
            $query = \App\Models\ServiceRecordRequest::query();
            $query->whereYear('created_at', substr($month, 0, 4))
                  ->whereMonth('created_at', substr($month, 5, 2));
            if ($userId) {
                $query->where('user_id', $userId);
            }
            if ($department) {
                $query->whereHas('user.employee', function ($q) use ($department) {
                    $q->where('department', $department);
                });
            }
            return $query->count();
        });

        $data = [
            'months' => $months,
            'leaveMonthly' => $leaveMonthly,
            'serviceRecordMonthly' => $serviceRecordMonthly,
        ];

        // Return only the selected report type
        if ($reportType === 'leave') {
            $data['series'] = $leaveMonthly;
        } elseif ($reportType === 'service_record') {
            $data['series'] = $serviceRecordMonthly;
        }

        return response()->json($data);
    }
    public function index()
    {
        $totalEmployees = Employee::count();
        $leaveApplications = LeaveApplication::where('status', 'Submitted')->count();
        $serviceRecordRequests = \App\Models\ServiceRecordRequest::where('request_status', 'Pending')->count();
        $accountsNeedingApproval = User::where('is_approved', false)->count();
        // ...existing code...

        // Recent Activity Feed: fetch latest 5 leave applications, 5 service record requests, and 5 new users
        $recentLeave = LeaveApplication::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'Leave Application',
                    'id' => $item->id,
                    'user' => $item->user ? $item->user->name : 'Unknown',
                    'status' => $item->status,
                    'timestamp' => $item->created_at,
                    'link' => route('leave_application.view', ['id' => $item->id]),
                ];
            });

        $recentService = \App\Models\ServiceRecordRequest::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'SR Request',
                    'id' => $item->id,
                    'user' => $item->user ? $item->user->name : 'Unknown',
                    'status' => $item->request_status,
                    'timestamp' => $item->created_at,
                    'link' => route('service-records.show', ['id' => $item->id]),
                ];
            });

        $recentUsers = User::orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'Employee',
                    'id' => $item->id,
                    'user' => $item->name,
                    'status' => 'Added',
                    'timestamp' => $item->created_at,
                    'link' => '#', // No user details route defined
                ];
            });

        // Merge and sort by timestamp descending
        $recentActivities = $recentLeave
            ->concat($recentService)
            ->concat($recentUsers)
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();
        $leaveApplications = LeaveApplication::where('status', 'Submitted')->count();
        $serviceRecordRequests = \App\Models\ServiceRecordRequest::where('request_status', 'Pending')->count();

        // For filter dropdowns
        $users = User::orderBy('name')->get();
        $departments = Employee::select('department')->distinct()->pluck('department');
        $reportTypes = [
            'leave' => 'Leave Applications',
            'service_record' => 'Service Record Requests',
        ];

        // Prepare monthly trend data for the last 12 months for both report types
        $months = collect(range(0, 11))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->reverse()->values();

        $leaveMonthly = $months->map(function ($month) {
            return LeaveApplication::where('status', 'Approved')
                ->whereYear('approved_at', substr($month, 0, 4))
                ->whereMonth('approved_at', substr($month, 5, 2))
                ->count();
        });
        $serviceRecordMonthly = $months->map(function ($month) {
            return \App\Models\ServiceRecordRequest::withTrashed()
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->where('request_status', 'claimed')
                ->count();
        });

        // Service Record Requests: Weekly, Monthly, Quarterly, Annually (claimed only)
        $now = now();
        $serviceWeekly = \App\Models\ServiceRecordRequest::withTrashed()
            ->where('request_status', 'claimed')
            ->whereBetween('completed_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count();
        $serviceMonthly = \App\Models\ServiceRecordRequest::withTrashed()
            ->where('request_status', 'claimed')
            ->whereYear('completed_at', $now->year)
            ->whereMonth('completed_at', $now->month)->count();
        $serviceQuarterly = \App\Models\ServiceRecordRequest::withTrashed()
            ->where('request_status', 'claimed')
            ->whereYear('completed_at', $now->year)
            ->whereBetween('completed_at', [
                $now->copy()->firstOfQuarter(),
                $now->copy()->lastOfQuarter()
            ])->count();
        $serviceAnnually = \App\Models\ServiceRecordRequest::withTrashed()
            ->where('request_status', 'claimed')
            ->whereYear('completed_at', $now->year)->count();

        // Leave Applications: Weekly, Monthly, Quarterly, Annually (approved only)
        $leaveWeekly = LeaveApplication::where('status', 'Approved')
            ->whereBetween('approved_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count();
        $leaveMonthlyCount = LeaveApplication::where('status', 'Approved')
            ->whereYear('approved_at', $now->year)
            ->whereMonth('approved_at', $now->month)->count();
        $leaveQuarterly = LeaveApplication::where('status', 'Approved')
            ->whereYear('approved_at', $now->year)
            ->whereBetween('approved_at', [
                $now->copy()->firstOfQuarter(),
                $now->copy()->lastOfQuarter()
            ])->count();
        $leaveAnnually = LeaveApplication::where('status', 'Approved')
            ->whereYear('approved_at', $now->year)->count();

        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'leaveApplications' => $leaveApplications,
            'serviceRecordRequests' => $serviceRecordRequests,
            'accountsNeedingApproval' => $accountsNeedingApproval,
            'users' => $users,
            'departments' => $departments,
            'reportTypes' => $reportTypes,
            'months' => $months,
            'leaveMonthly' => $leaveMonthly,
            'serviceRecordMonthly' => $serviceRecordMonthly,
            'serviceWeekly' => $serviceWeekly,
            'serviceMonthlyCount' => $serviceMonthly,
            'serviceQuarterly' => $serviceQuarterly,
            'serviceAnnually' => $serviceAnnually,
            'leaveWeekly' => $leaveWeekly,
            'leaveMonthlyCount' => $leaveMonthlyCount,
            'leaveQuarterly' => $leaveQuarterly,
            'leaveAnnually' => $leaveAnnually,
            'recentActivities' => $recentActivities,
        ]);
    }
}
