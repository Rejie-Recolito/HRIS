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

    public function submit()
    {
        $validated = $this->validate();
        $validated['status'] = 'Submitted';
        LeaveApplication::create($validated);
        $this->reset();
        $this->dispatch('refreshTable');
        session()->flash('success', 'Leave application submitted successfully!');
    }

    public function getLastApplicationProperty()
    {
        return LeaveApplication::orderByDesc('created_at')->first();
    }

    public function render()
    {
        return view('livewire.leave-application-form');
    }
}
