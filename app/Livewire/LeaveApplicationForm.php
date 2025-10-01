<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Auth;


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
