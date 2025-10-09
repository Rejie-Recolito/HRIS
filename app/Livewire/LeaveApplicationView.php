<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveApplication;


class LeaveApplicationView extends Component
{
    public $id; // Define the public property to accept the parameter

   public $leaveApplication;

public function mount($id)
{
    $this->leaveApplication = LeaveApplication::findOrFail($id);
}
    
    public function render()
{
    return view('livewire.leave-application-view')->layout('layouts.app'); // Corrected layout path
}
}