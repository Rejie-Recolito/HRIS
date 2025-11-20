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
    public $leaveCardDetails = [];

public function mount($id)
{
    $this->leaveApplication = LeaveApplication::findOrFail($id);
    // try to resolve the employee for this leave application (by user_id)
    $employee = null;
    if (!empty($this->leaveApplication->user_id)) {
        $employee = Employee::where('user_id', $this->leaveApplication->user_id)->first();
    }

    if ($employee) {
        // Opening Balance
        $openingBalance = [
            'vacation' => 12,
            'sick' => 15,
        ];

        // Sum of assigned credits (only positive amounts)
        $credits = LeaveCredit::where('employee_id', $employee->id)->get();
        $earnedCredits = [
            'vacation' => $credits->where('type', 'vacation')->where('amount', '>', 0)->sum('amount'),
            'sick' => $credits->where('type', 'sick')->where('amount', '>', 0)->sum('amount'),
        ];

        // Total Earned = Opening Balance + Earned Credits
        $totalEarned = [
            'vacation' => $openingBalance['vacation'] + $earnedCredits['vacation'],
            'sick' => $openingBalance['sick'] + $earnedCredits['sick'],
        ];

        // Calculate availed from negative leave credits (consumption records)
        $availed = [
            'vacation' => abs($credits->where('type', 'vacation')->where('amount', '<', 0)->sum('amount')),
            'sick' => abs($credits->where('type', 'sick')->where('amount', '<', 0)->sum('amount')),
        ];

        // Balance = Total Earned - Availed
        $balance = [
            'vacation' => $totalEarned['vacation'] - $availed['vacation'],
            'sick' => $totalEarned['sick'] - $availed['sick'],
        ];

        // Set totals for backward compatibility
        $this->vacationTotal = $totalEarned['vacation'];
        $this->sickTotal = $totalEarned['sick'];

        // Store detailed breakdown for display
        $this->leaveCardDetails = [
            'vacation' => [
                'opening' => $openingBalance['vacation'],
                'earned' => $earnedCredits['vacation'],
                'total' => $totalEarned['vacation'],
                'availed' => $availed['vacation'],
                'balance' => $balance['vacation'],
            ],
            'sick' => [
                'opening' => $openingBalance['sick'],
                'earned' => $earnedCredits['sick'],
                'total' => $totalEarned['sick'],
                'availed' => $availed['sick'],
                'balance' => $balance['sick'],
            ],
        ];
    } else {
        $this->vacationTotal = 0;
        $this->sickTotal = 0;
        $this->leaveCardDetails = [
            'vacation' => ['opening' => 0, 'earned' => 0, 'total' => 0, 'availed' => 0, 'balance' => 0],
            'sick' => ['opening' => 0, 'earned' => 0, 'total' => 0, 'availed' => 0, 'balance' => 0],
        ];
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