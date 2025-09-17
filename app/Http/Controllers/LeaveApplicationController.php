<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;

class LeaveApplicationController extends Controller
{
    public function create()
    {
        return view('leave_user');
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

        LeaveApplication::create($validated);

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
}
