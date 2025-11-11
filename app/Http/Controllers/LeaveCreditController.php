<?php

namespace App\Http\Controllers;

use App\Models\LeaveCredit;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveCreditController extends Controller
{
    public function show($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        $credits = LeaveCredit::where('employee_id', $employeeId)->orderBy('created_at', 'desc')->get();

        $totals = [
            'vacation' => $credits->where('type', 'vacation')->sum('amount'),
            'sick' => $credits->where('type', 'sick')->sum('amount'),
        ];

        return view('admin.leave-card', compact('employee', 'credits', 'totals'));
    }

    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'type' => 'required|in:vacation,sick',
            'amount' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($employeeId);

        $credit = LeaveCredit::create([
            'employee_id' => $employee->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'assigned_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('employees.index')->with('success', 'Leave credit assigned successfully.');
    }
}
