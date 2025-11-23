<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\ServiceRecordRequest;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get stats for leave applications
        $leaveStats = $this->getStats(LeaveApplication::class);
        // Get stats for service record requests
        $serviceRecordStats = $this->getStats(ServiceRecordRequest::class);

        return view('reports.index', compact('leaveStats', 'serviceRecordStats'));
    }

    private function getStats($modelClass)
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfQuarter = $now->copy()->firstOfQuarter();
        $startOfYear = $now->copy()->startOfYear();

        return [
            'weekly' => Cache::remember($modelClass.'_weekly', 600, function () use ($modelClass, $startOfWeek, $now) {
                return $modelClass::whereBetween('created_at', [$startOfWeek, $now])->count();
            }),
            'monthly' => Cache::remember($modelClass.'_monthly', 600, function () use ($modelClass, $startOfMonth, $now) {
                return $modelClass::whereBetween('created_at', [$startOfMonth, $now])->count();
            }),
            'quarterly' => Cache::remember($modelClass.'_quarterly', 600, function () use ($modelClass, $startOfQuarter, $now) {
                return $modelClass::whereBetween('created_at', [$startOfQuarter, $now])->count();
            }),
            'annually' => Cache::remember($modelClass.'_annually', 600, function () use ($modelClass, $startOfYear, $now) {
                return $modelClass::whereBetween('created_at', [$startOfYear, $now])->count();
            }),
        ];
    }
}
