<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;

class AdminUserApprovals extends Component
{
    public $pendingUsers = [];
    public $confirmingUserId = null;
    public $confirmingUserName = null;

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
    }

    public function approveUser($id)
    {
        $targetId = $id ?? $this->confirmingUserId;
        $user = User::findOrFail($targetId);
        $user->is_approved = true;
        $user->save();

        // reset modal state
        $this->confirmingUserId = null;
        $this->confirmingUserName = null;

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
