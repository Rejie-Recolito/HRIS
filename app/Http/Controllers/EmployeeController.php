<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Show the user-facing employee information form.
     */

    public function index()
    {
        $employees = Employee::all(); // Fetch data using the Employee model
        return view('admin.employees', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|string|max:255',
            'sex' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'designation' => 'required|string|max:255',
            'place_of_assignment' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Prevent duplicate employee records for the same user.
        $existing = Employee::where('user_id', Auth::id())->first();
        if ($existing) {
            // Update existing record instead of creating a new one
            $existing->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'employee' => $existing,
                    'message' => 'Employee updated instead of creating duplicate.'
                ]);
            }

            return redirect()->back()->with('success', 'Employee updated successfully.');
        }

        $employee = Employee::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'employee' => $employee
            ]);
        }

        return redirect()->back()->with('success', 'Employee added successfully.');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.edit-employee', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|string|max:255',
            'sex' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'designation' => 'required|string|max:255',
            'place_of_assignment' => 'required|string|max:255',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function showUserForm()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        return view('add-user-information', compact('employee'));
    }
}