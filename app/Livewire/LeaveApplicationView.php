<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveApplication;
use App\Models\Employee;
use App\Models\LeaveCredit;


class LeaveApplicationView extends Component
{
    public $id; // Define the public property to accept the parameter

   public $leaveApplication;
    public $vacationTotal = 0;
    public $sickTotal = 0;

public function mount($id)
{
    $this->leaveApplication = LeaveApplication::findOrFail($id);
    // try to resolve the employee for this leave application (by user_id)
    $employee = null;
    if (!empty($this->leaveApplication->user_id)) {
        $employee = Employee::where('user_id', $this->leaveApplication->user_id)->first();
    }

    if ($employee) {
        $credits = LeaveCredit::where('employee_id', $employee->id)->get();
        $this->vacationTotal = (int) $credits->where('type', 'vacation')->sum('amount');
        $this->sickTotal = (int) $credits->where('type', 'sick')->sum('amount');
    } else {
        $this->vacationTotal = 0;
        $this->sickTotal = 0;
    }

    // ensure the leaveApplication fields have defaults so Blade inputs show sensible values
    $this->leaveApplication->vacation_total_earned = $this->leaveApplication->vacation_total_earned ?? $this->vacationTotal;
    $this->leaveApplication->sick_total_earned = $this->leaveApplication->sick_total_earned ?? $this->sickTotal;
}
    
    public function render()
{
    return view('livewire.leave-application-view');
}
}