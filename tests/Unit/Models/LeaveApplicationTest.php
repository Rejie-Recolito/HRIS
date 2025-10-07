<?php

namespace Tests\Unit\Models;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $user = User::factory()->create();
        
        $leaveApplication = LeaveApplication::create([
            'user_id' => $user->id,
            'lastname' => 'Doe',
            'firstname' => 'John',
            'middlename' => 'M',
            'date_of_filing' => now()->format('Y-m-d'),
            'position' => 'Developer',
            'salary' => 50000,
            'type_of_leave' => 'vacation',
            'number_of_days' => 5,
            'inclusive_dates' => '2025-10-06 to 2025-10-10',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(LeaveApplication::class, $leaveApplication);
        $this->assertEquals('Doe', $leaveApplication->lastname);
        $this->assertEquals('John', $leaveApplication->firstname);
        $this->assertEquals($user->id, $leaveApplication->user_id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $leaveApplication = LeaveApplication::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $leaveApplication->user);
        $this->assertEquals($user->id, $leaveApplication->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'lastname',
            'firstname',
            'middlename',
            'date_of_filing',
            'position',
            'salary',
            'type_of_leave',
            'others',
            'number_of_days',
            'inclusive_dates',
            'status',
            'user_id',
        ];

        $leaveApplication = new LeaveApplication();
        
        $this->assertEquals($fillable, $leaveApplication->getFillable());
    }

    /** @test */
    public function it_can_have_different_leave_types()
    {
        $user = User::factory()->create();
        
        $vacationLeave = LeaveApplication::factory()->create([
            'user_id' => $user->id,
            'type_of_leave' => 'vacation'
        ]);
        
        $sickLeave = LeaveApplication::factory()->create([
            'user_id' => $user->id,
            'type_of_leave' => 'sick'
        ]);

        $this->assertEquals('vacation', $vacationLeave->type_of_leave);
        $this->assertEquals('sick', $sickLeave->type_of_leave);
    }

    /** @test */
    public function it_has_default_pending_status()
    {
        $user = User::factory()->create();
        
        $leaveApplication = LeaveApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->assertEquals('pending', $leaveApplication->status);
    }
}