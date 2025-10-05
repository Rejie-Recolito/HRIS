<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;

class LeaveApplicationController extends Controller
{
    // Admin: approve a leave application and download PDF
    public function approve($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Approved';
            $leave->save();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_application_letter', compact('leave'));
            return $pdf->download('leave_application_letter_'.$leave->lastname.'_'.$leave->firstname.'.pdf');
        }
        return redirect()->back()->with('error', 'Leave application must be Under Review to approve.');
    }
    public function create()
    {
        $lastApplication = Auth::user()->leaveApplications()->latest()->first();
        return view('leave_user', compact('lastApplication'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'date_of_filing' => 'required|date',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'type_of_leave' => 'required|string',
            'others' => 'nullable|string|max:255',
            'number_of_days' => 'required|integer|min:1',
            'inclusive_dates' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        // Check if the user has a pending leave application
        $existingApplication = LeaveApplication::where('user_id', Auth::id())
            ->whereIn('status', ['Under Review', 'Submitted'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You already have a leave application under review or submitted.');
        }

        $leaveApplication = LeaveApplication::create($validated);

        // Notify admin with a custom message
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            
            $admin->notifications()->create([
                'type' => AdminNotification::class,
                'data' => [
                    'message' => Auth::user()->name . ' has requested a leave.',
                    'leave_application_id' => $leaveApplication->id,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Leave application submitted successfully!');
    }

    // Admin: show all leave applications
    public function index()
    {
        $leaveApplications = LeaveApplication::all();
        return view('admin.leave', compact('leaveApplications'));
    }

    // Admin: accept a leave application (for demo, just delete)
    public function accept($id)
    {
        $leave = LeaveApplication::findOrFail($id);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_application_letter', compact('leave'));

        // Delete after generating PDF
        $leave->delete();

        // Download PDF
        return $pdf->download('leave_application_letter_'.$leave->lastname.'_'.$leave->firstname.'.pdf');
    }

    // Admin: delete a leave application
    public function delete($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->delete();
        return redirect()->back()->with('success', 'Leave application deleted.');
    }

    public function downloadAllPdf()
    {
        $leaveApplications = LeaveApplication::all(); // Fetch all leave applications
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_applications', compact('leaveApplications'));
        return $pdf->download('leave_applications.pdf');
    }

    public function downloadPdf($id)
    {
        // Fetch the specific leave application by ID
        $leave = LeaveApplication::findOrFail($id);

        // Generate the PDF using the view and the leave application data
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.leave_applications', compact('leave'));

        // Return the PDF for download
        return $pdf->download('leave_application_' . $id . '.pdf');
    }
}
