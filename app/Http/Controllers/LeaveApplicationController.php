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
}
