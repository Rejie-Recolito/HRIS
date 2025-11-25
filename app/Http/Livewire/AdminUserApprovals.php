<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;

class AdminUserApprovals extends Component
{
    public $pendingUsers = [];
    public $confirmingUserId = null;
    public $confirmingUserName = null;
    public $confirmingUserEmployeeId = null;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function mount()
    {
        $this->loadPending();
    }

    public function loadPending()
    {
        $this->pendingUsers = User::where('is_approved', false)->get();
    }

    public function confirmApprove($id)
    {
        $user = User::findOrFail($id);
        $this->confirmingUserId = $id;
        $this->confirmingUserName = $user->name;
        $this->confirmingUserEmployeeId = $user->employee_id;
    }

    public function approveUser($id)
    {
        $targetId = $id ?? $this->confirmingUserId;
        $user = User::findOrFail($targetId);
        // Prevent approving if another approved user already has the same employee_id
        if ($user->employee_id) {
            $conflict = User::where('employee_id', $user->employee_id)
                ->where('is_approved', true)
                ->where('id', '!=', $user->id)
                ->first();

            if ($conflict) {
                // keep modal open and show an error message to admin
                session()->flash('approval_error', "Cannot approve: another approved account (" . $conflict->email . ") already uses Employee ID {$user->employee_id}.");
                return;
            }
        }
        $user->is_approved = true;
        $user->save();

        // reset modal state
        $this->confirmingUserId = null;
        $this->confirmingUserName = null;
        $this->confirmingUserEmployeeId = null;

        $this->loadPending();
        session()->flash('success', 'User approved successfully.');
        $this->emit('refreshUsers');
    }

    public function render()
    {
        return view('livewire.admin-user-approvals', [
            'pendingUsers' => $this->pendingUsers,
        ]);
    }
}
