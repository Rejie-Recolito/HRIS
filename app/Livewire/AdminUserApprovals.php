<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class AdminUserApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $confirmingUserId = null;
    public $confirmingUserName = null;
    public $confirmingUserEmployeeId = null;

    protected $listeners = ['refreshUsers' => '$refresh'];

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmApprove($id)
    {
        $user = User::findOrFail($id);
        $this->confirmingUserId = $id;
        $this->confirmingUserName = $user->name;
        $this->confirmingUserEmployeeId = $user->employee_id;
    }

    public function denyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('success', 'User denied and deleted.');
        $this->confirmingUserEmployeeId = null;
        $this->dispatch('refreshUsers');
    }

    public function approveUser($id = null)
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

        session()->flash('success', 'User approved successfully.');
        $this->dispatch('refreshUsers');
    }

    public function getUsersProperty()
    {
        return User::where('is_approved', false)
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin-user-approvals', [
            'pendingUsers' => $this->users,
        ]);
    }
}
