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
    }

    public function denyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('success', 'User denied and deleted.');
        $this->dispatch('refreshUsers');
    }

    public function approveUser($id = null)
    {
        $targetId = $id ?? $this->confirmingUserId;
        $user = User::findOrFail($targetId);
        $user->is_approved = true;
        $user->save();

        // reset modal state
        $this->confirmingUserId = null;
        $this->confirmingUserName = null;

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
