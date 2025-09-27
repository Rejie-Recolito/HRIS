<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\LeaveApplication;


class LeaveApplicationsTable extends Component
{
    public $leaveApplications;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function mount()
    {
        $this->leaveApplications = LeaveApplication::all();
    }

    public function updated()
    {
        $this->leaveApplications = LeaveApplication::all();
    }

    public function accept($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Submitted') {
            $leave->status = 'Under Review';
            $leave->save();
            $this->leaveApplications = LeaveApplication::all();
            session()->flash('success', 'Leave application set to Under Review.');
        }
    }

    public function approve($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Approved';
            $leave->save();
            $this->leaveApplications = LeaveApplication::all();
            session()->flash('success', 'Leave application approved.');
        }
    }

    public function deny($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Denied';
            $leave->save();
            $this->leaveApplications = LeaveApplication::all();
            session()->flash('success', 'Leave application denied.');
        }
    }

    public function delete($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->delete();
        $this->leaveApplications = LeaveApplication::all();
        session()->flash('success', 'Leave application deleted.');
    }

    public function render()
    {
        return view('livewire.leave-applications-table', [
            'leaveApplications' => $this->leaveApplications
        ]);
    }
}
