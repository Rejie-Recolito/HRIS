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
        $this->leaveApplications = LeaveApplication::where('is_deleted', false)->orderByDesc('date_of_filing')->get();
    }

    public function updated()
    {
        $this->leaveApplications = LeaveApplication::where('is_deleted', false)->orderByDesc('date_of_filing')->get();
    }

    public function accept($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Submitted') {
            $leave->status = 'Under Review';
            $leave->save();
            $this->leaveApplications = LeaveApplication::where('is_deleted', false)->orderByDesc('date_of_filing')->get();
            session()->flash('success', 'Leave application set to Under Review.');
        }
    }

    public function approve($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Approved';
            $leave->action_date = now();
            $leave->save();
            $this->leaveApplications = LeaveApplication::where('is_deleted', false)->orderByDesc('date_of_filing')->get();
            session()->flash('success', 'Leave application approved.');
        }
    }

    public function deny($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($leave->status === 'Under Review') {
            $leave->status = 'Denied';
            $leave->action_date = now();
            $leave->save();
            $this->leaveApplications = LeaveApplication::where('is_deleted', false)->orderByDesc('date_of_filing')->get();
            session()->flash('success', 'Leave application denied.');
        }
    }

    public function delete($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->is_deleted = true;
        $leave->save();
    $this->leaveApplications = LeaveApplication::where('is_deleted', false)->orderByDesc('date_of_filing')->get();
        session()->flash('success', 'Leave application deleted.');
    }

    public function render()
    {
        return view('livewire.leave-applications-table', [
            'leaveApplications' => $this->leaveApplications
        ]);
    }
}
