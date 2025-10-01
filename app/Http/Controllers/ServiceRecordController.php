<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ServiceRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'salary' => 'required|numeric',
            'date_of_birth' => 'required|date',
            'job_title' => 'required|string',
            'place_of_birth' => 'required|string',
            'office' => 'required|string',
            'status' => 'required|string',
            'date_of_service' => 'required|date',
            'place_of_assignment' => 'required|string',
        ]);

        $serviceRecord = ServiceRecord::create($validated);

        // Notify admin with a custom message
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            $admin->notifications()->create([
                'type' => AdminNotification::class,
                'data' => [
                    'message' => Auth::user()->name . ' has requested a service record.',
                    'service_record_id' => $serviceRecord->id,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Service record created successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceRecords = ServiceRecord::all();
        return view('admin.service_record', compact('serviceRecords'));
    }
}