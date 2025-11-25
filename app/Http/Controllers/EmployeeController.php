<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    /**
     * Show the user-facing employee information form.
     */

    public function index()
    {
        $employees = Employee::orderBy('lastname')->get(); // Fetch ordered employees
        $count = $employees->count();
        \Log::info('Admin viewing employees list', ['count' => $count]);

        // Ensure the navigation view always has a $notifications variable.
        if (Schema::hasTable('notifications')) {
            try {
                $notifications = Notification::latest()->get();
            } catch (\Throwable $e) {
                $notifications = collect();
            }
        } else {
            $notifications = collect();
        }

        return view('admin.employees', compact('employees', 'count', 'notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'department_other' => 'nullable|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|in:Permanent,Temporary,Casual,Contractual,Job Order,Probationary,Co-Terminus,Other',
            'sex' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'age' => 'required|integer|min:0',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'place_of_assignment' => 'required|string|max:255',
            'phone_number' => ['required','regex:/^09[0-9]{9}$/'],
            'email_address' => ['required','email','max:255','regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'],
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        // If department is 'Other', use department_other value
        if (isset($data['department']) && $data['department'] === 'Other' && !empty($data['department_other'])) {
            $data['department'] = $data['department_other'];
        }
        unset($data['department_other']);

        // Prevent duplicate employee records for the same user.
        $existing = Employee::where('user_id', Auth::id())->first();
        if ($existing) {
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
        \Log::info('Employee created via store', ['employee_id' => $employee->id, 'user_id' => $employee->user_id]);

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
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'department_other' => 'nullable|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|in:Permanent,Temporary,Casual,Contractual,Job Order,Probationary,Co-Terminus,Other',
            'sex' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'salary' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'place_of_assignment' => 'required|string|max:255',
            'phone_number' => ['required','regex:/^09[0-9]{9}$/'],
            'email_address' => ['required','email','max:255','regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'],
        ]);

        $data = $request->all();
        if (isset($data['department']) && $data['department'] === 'Other' && !empty($data['department_other'])) {
            $data['department'] = $data['department_other'];
        }
        unset($data['department_other']);

        $employee = Employee::findOrFail($id);
        $employee->update($data);

        // Redirect based on user role
        if (Auth::user()->is_admin) {
            return redirect()->route('employees.edit', $id)->with('success', 'Employee Info updated successfully.');
        } else {
            return redirect()->route('add-user-information.user')->with('success', 'Employee Profile updated successfully.');
        }
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

    /**
     * Show service record management page for an employee
     */
    public function showServiceRecord($id)
    {
        $employee = Employee::findOrFail($id);
        $serviceRecords = ServiceRecord::where('employee_id', $id)
            ->orderBy('service_from', 'asc')
            ->get();
        
        return view('admin.employee_service_record_new', compact('employee', 'serviceRecords'));
    }

    /**
     * Store a new service record entry
     */
    public function storeServiceRecord(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $validated = $request->validate([
            'service_from' => 'required|date',
            'service_to' => 'nullable',
            'appointment_designation' => 'required|string|max:255',
            'appointment_status' => 'required|in:Permanent,Temporary,Casual,Contractual,Job Order,Probationary,Co-Terminus,Other',
            'appointment_salary' => 'required|numeric|min:0',
            'station_place' => 'required|string|max:255',
            'leave_of_absence' => 'nullable|string|max:255',
            'separation_date' => 'nullable|date',
            'separation_cause' => 'nullable|string|max:500',
        ]);

        if ($request->has('service_to_present')) {
            $validated['service_to'] = 'Present';
        }

        $validated['employee_id'] = $employee->id;
        $validated['user_id'] = $employee->user_id;

        ServiceRecord::create($validated);

        return redirect()->route('employees.service_record', $id)
            ->with('success', 'Service record entry added successfully.');
    }

    /**
     * Update a service record entry
     */
    public function updateServiceRecord(Request $request, $employeeId, $recordId)
    {
        $record = ServiceRecord::where('employee_id', $employeeId)
            ->where('id', $recordId)
            ->firstOrFail();
        
        $validated = $request->validate([
            'service_from' => 'required|date',
            'service_to' => 'nullable',
            'appointment_designation' => 'required|string|max:255',
            'appointment_status' => 'required|in:Permanent,Temporary,Casual,Contractual,Job Order,Probationary,Co-Terminus,Other',
            'appointment_salary' => 'required|numeric|min:0',
            'station_place' => 'required|string|max:255',
            'leave_of_absence' => 'nullable|string|max:255',
            'separation_date' => 'nullable|date',
            'separation_cause' => 'nullable|string|max:500',
        ]);

        if ($request->has('service_to_present')) {
            $validated['service_to'] = 'Present';
        }

        $record->update($validated);

        return redirect()->route('employees.service_record', $employeeId)
            ->with('success', 'Service record entry updated successfully.');
    }

    /**
     * Delete a service record entry
     */
    public function deleteServiceRecord($employeeId, $recordId)
    {
        $record = ServiceRecord::where('employee_id', $employeeId)
            ->where('id', $recordId)
            ->firstOrFail();
        
        $record->delete();

        return redirect()->route('employees.service_record', $employeeId)
            ->with('success', 'Service record entry deleted successfully.');
    }
}