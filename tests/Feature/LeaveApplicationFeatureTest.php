<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_leave_application()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $leaveData = [
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
        ];

        $response = $this->post('/leave-applications', $leaveData);

        $this->assertDatabaseHas('leave_applications', [
            'user_id' => $user->id,
            'lastname' => 'Doe',
            'firstname' => 'John',
            'type_of_leave' => 'vacation',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function unauthenticated_user_cannot_create_leave_application()
    {
        $leaveData = [
            'lastname' => 'Doe',
            'firstname' => 'John',
            'type_of_leave' => 'vacation',
        ];

        $response = $this->post('/leave-applications', $leaveData);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('leave_applications', 0);
    }

    /** @test */
    public function leave_application_requires_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/leave-applications', []);

        $response->assertSessionHasErrors([
            'lastname',
            'firstname',
            'date_of_filing',
            'position',
            'salary',
            'type_of_leave',
            'number_of_days',
            'inclusive_dates',
        ]);
    }

    /** @test */
    public function admin_can_view_all_leave_applications()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        
        $leaveApplication = LeaveApplication::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($admin);

        $response = $this->get('/admin/leave-applications');

        $response->assertStatus(200);
        $response->assertSee($leaveApplication->lastname);
        $response->assertSee($leaveApplication->firstname);
    }

    /** @test */
    public function user_can_view_their_own_leave_applications()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $userLeaveApplication = LeaveApplication::factory()->create(['user_id' => $user->id]);
        $otherLeaveApplication = LeaveApplication::factory()->create(['user_id' => $otherUser->id]);
        
        $this->actingAs($user);

        $response = $this->get('/my-leave-applications');

        $response->assertStatus(200);
        $response->assertSee($userLeaveApplication->lastname);
        $response->assertDontSee($otherLeaveApplication->lastname);
    }

    /** @test */
    public function admin_can_update_leave_application_status()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $leaveApplication = LeaveApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($admin);

        $response = $this->patch("/admin/leave-applications/{$leaveApplication->id}/status", [
            'status' => 'approved'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('leave_applications', [
            'id' => $leaveApplication->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_leave_application_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $leaveApplication = LeaveApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($user);

        $response = $this->patch("/admin/leave-applications/{$leaveApplication->id}/status", [
            'status' => 'approved'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('leave_applications', [
            'id' => $leaveApplication->id,
            'status' => 'pending'
        ]);
    }
}