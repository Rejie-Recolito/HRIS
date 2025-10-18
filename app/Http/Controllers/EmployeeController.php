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
    public function showUserForm(Request $request)
    {
    $employee = Employee::where('user_id', Auth::id())->first();
    $edit = $request->boolean('edit', false);
    return view('add-user-information', compact('employee', 'edit'));
    }
    public function updateUserInfo(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            return redirect()->route('add-user-information.user')->with('error', 'No employee record found.');
        }
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
        $employee->update($request->all());
        return redirect()->route('add-user-information.user')->with('success', 'Employee information updated successfully.');
    }
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
        Employee::create($data);

        // Redirect to the user profile page to show the saved info (form will be hidden)
        return redirect()->route('add-user-information.user')->with('success', 'Employee added successfully.');
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

}