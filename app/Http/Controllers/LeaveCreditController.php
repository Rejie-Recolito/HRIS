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
        
        $credits = LeaveCredit::where('employee_id', $employeeId)
            ->with('source')
            ->orderBy('created_at', 'asc')
            ->get();

        // Opening Balance
        $openingBalance = [
            'vacation' => 12,
            'sick' => 15,
        ];

        // Prepare history for vacation leave
        $vacationHistory = [];
        $vacationBalance = $openingBalance['vacation'];
        
        // Add credits to vacation history
        foreach ($credits->where('type', 'vacation') as $credit) {
            $vacationBalance += $credit->amount;
            
            // Handle negative credits as availed days
            if ($credit->amount < 0) {
                // Get the type of leave from the source (LeaveApplication)
                $particulars = 'Vacation Leave'; // Default for vacation type
                $displayDate = $credit->created_at;
                
                if ($credit->source && $credit->source instanceof \App\Models\LeaveApplication) {
                    $particulars = $credit->source->type_of_leave;
                    // Use the approved_at date if available, otherwise fall back to created_at
                    $displayDate = $credit->source->approved_at ?? $credit->created_at;
                } elseif ($credit->notes) {
                    // Try to extract leave type from notes if source is not available
                    if (str_contains($credit->notes, 'leave_application:')) {
                        $particulars = 'Vacation Leave';
                    }
                }
                
                $vacationHistory[] = [
                    'date' => $displayDate,
                    'particulars' => $particulars,
                    'credited' => 0,
                    'availed' => abs($credit->amount),
                    'balance' => $vacationBalance,
                    'type' => 'deduction',
                    'id' => $credit->id,
                ];
            } else {
                // Check if this is a reversal (has source)
                if ($credit->source && $credit->source instanceof \App\Models\LeaveApplication) {
                    $particulars = $credit->source->type_of_leave . ' (Reversal)';
                } elseif ($credit->notes && (str_contains($credit->notes, 'Reversal') || str_contains($credit->notes, 'Consumed'))) {
                    // Parse the leave type from notes for deleted leave applications
                    $particulars = ($credit->type === 'vacation' ? 'Vacation Leave' : 'Sick Leave');
                    if (str_contains($credit->notes, 'Reversal')) {
                        $particulars .= ' (Reversal)';
                    }
                } else {
                    $particulars = $credit->notes ?? 'Monthly Credit';
                }
                
                $vacationHistory[] = [
                    'date' => $credit->created_at,
                    'particulars' => $particulars,
                    'credited' => $credit->amount,
                    'availed' => 0,
                    'balance' => $vacationBalance,
                    'type' => 'credit',
                    'id' => $credit->id,
                ];
            }
        }

        // Prepare history for sick leave
        $sickHistory = [];
        $sickBalance = $openingBalance['sick'];
        
        // Add credits to sick history
        foreach ($credits->where('type', 'sick') as $credit) {
            $sickBalance += $credit->amount;
            
            // Handle negative credits as availed days
            if ($credit->amount < 0) {
                // Get the type of leave from the source (LeaveApplication)
                $particulars = 'Sick Leave'; // Default for sick type
                $displayDate = $credit->created_at;
                
                if ($credit->source && $credit->source instanceof \App\Models\LeaveApplication) {
                    $particulars = $credit->source->type_of_leave;
                    // Use the approved_at date if available, otherwise fall back to created_at
                    $displayDate = $credit->source->approved_at ?? $credit->created_at;
                } elseif ($credit->notes) {
                    // Try to extract leave type from notes if source is not available
                    if (str_contains($credit->notes, 'leave_application:')) {
                        $particulars = 'Sick Leave';
                    }
                }
                
                $sickHistory[] = [
                    'date' => $displayDate,
                    'particulars' => $particulars,
                    'credited' => 0,
                    'availed' => abs($credit->amount),
                    'balance' => $sickBalance,
                    'type' => 'deduction',
                    'id' => $credit->id,
                ];
            } else {
                // Check if this is a reversal (has source)
                if ($credit->source && $credit->source instanceof \App\Models\LeaveApplication) {
                    $particulars = $credit->source->type_of_leave . ' (Reversal)';
                } elseif ($credit->notes && (str_contains($credit->notes, 'Reversal') || str_contains($credit->notes, 'Consumed'))) {
                    // Parse the leave type from notes for deleted leave applications
                    $particulars = ($credit->type === 'sick' ? 'Sick Leave' : 'Vacation Leave');
                    if (str_contains($credit->notes, 'Reversal')) {
                        $particulars .= ' (Reversal)';
                    }
                } else {
                    $particulars = $credit->notes ?? 'Monthly Credit';
                }
                
                $sickHistory[] = [
                    'date' => $credit->created_at,
                    'particulars' => $particulars,
                    'credited' => $credit->amount,
                    'availed' => 0,
                    'balance' => $sickBalance,
                    'type' => 'credit',
                    'id' => $credit->id,
                ];
            }
        }

        // Sum of assigned credits
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

        return view('admin.leave-card', compact('employee', 'credits', 'openingBalance', 'earnedCredits', 'totalEarned', 'availed', 'balance', 'vacationHistory', 'sickHistory'));
    }

    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'type' => 'required|in:vacation,sick',
            'amount' => 'required|numeric|min:0',
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

        return redirect()->route('employees.leave_card', $employeeId)->with('success', 'Leave credit assigned successfully.');
    }
}
