<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveCredit;
use App\Models\Employee;


class LeaveApplicationForm extends Component
{
    public $lastname, $firstname, $middlename, $date_of_filing, $position, $salary, $type_of_leave, $others, $number_of_days, $inclusive_dates;

    protected $rules = [
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
    ];

    /**
     * The last leave application of the logged-in user.
     *
     * @var \App\Models\LeaveApplication|null
     */
    public $lastApplication;

    /**
     * The status message to display after submission.
     *
     * @var string|null
     */
    public $submitStatus = null;

    public function mount()
    {
        // Fetch the last application for the logged-in user
        $this->lastApplication = LeaveApplication::where('user_id', Auth::id())
            ->latest()
            ->first();

        // Debugging: Log the last application
        logger()->info('Last Application:', ['lastApplication' => $this->lastApplication]);
    }

    /**
     * Get the current leave credits totals for the authenticated user's employee record.
     * Returns an array with 'vacation' and 'sick' integer totals.
     */
    public function getLeaveTotalsProperty()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (! $employee) {
            return [
                'vacation' => ['opening' => 0, 'earned' => 0, 'total' => 0, 'availed' => 0, 'balance' => 0],
                'sick' => ['opening' => 0, 'earned' => 0, 'total' => 0, 'availed' => 0, 'balance' => 0],
            ];
        }

        // Opening Balance
        $openingBalance = [
            'vacation' => 12,
            'sick' => 15,
        ];

        // Get all credits for this employee
        $credits = LeaveCredit::where('employee_id', $employee->id)->get();
        
        // Sum of assigned credits (positive amounts only)
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

        return [
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
    }

    public function submit()
    {

        // Check if the user has a pending leave application
        if ($this->lastApplication && in_array($this->lastApplication->status, ['Under Review', 'Submitted'])) {
            session()->flash('error', 'You already have a pending leave application. Please wait for it to be approved or denied.');
            return;
        }

        // Validate and create the leave application
        $validated = $this->validate();
        $validated['status'] = 'Submitted';
        $validated['user_id'] = Auth::id();
        LeaveApplication::create($validated);

        // Reset the form and refresh the last application
        $this->reset();
        $this->mount(); // Refresh the last application

        // Set the submit status message
        $this->submitStatus = 'Leave application submitted successfully!';
        session()->flash('success', $this->submitStatus);
    }



    public function getLastApplicationProperty()
    {
        return LeaveApplication::where('user_id', Auth::id()) // Filter by logged-in user
            ->orderByDesc('created_at')
            ->first();
    }


    public function render()
    {
        return view('livewire.leave-application-form');
    }
}
