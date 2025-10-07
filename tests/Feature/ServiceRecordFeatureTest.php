<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_service_record()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $serviceData = [
            'name' => 'John Doe',
            'age' => 30,
            'salary' => 60000,
            'date_of_birth' => '1994-01-01',
            'job_title' => 'Software Developer',
            'place_of_birth' => 'Manila',
            'office' => 'IT Department',
            'status' => 'active',
            'date_of_service' => '2020-01-01',
            'place_of_assignment' => 'Main Office',
        ];

        $response = $this->post('/service-records', $serviceData);

        $this->assertDatabaseHas('service_records', [
            'user_id' => $user->id,
            'name' => 'John Doe',
            'job_title' => 'Software Developer',
            'office' => 'IT Department',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function unauthenticated_user_cannot_create_service_record()
    {
        $serviceData = [
            'name' => 'John Doe',
            'job_title' => 'Developer',
        ];

        $response = $this->post('/service-records', $serviceData);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('service_records', 0);
    }

    /** @test */
    public function service_record_requires_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/service-records', []);

        $response->assertSessionHasErrors([
            'name',
            'age',
            'salary',
            'date_of_birth',
            'job_title',
            'place_of_birth',
            'office',
            'status',
            'date_of_service',
            'place_of_assignment',
        ]);
    }

    /** @test */
    public function admin_can_view_all_service_records()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        
        $serviceRecord = ServiceRecord::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($admin);

        $response = $this->get('/admin/service-records');

        $response->assertStatus(200);
        $response->assertSee($serviceRecord->name);
        $response->assertSee($serviceRecord->job_title);
    }

    /** @test */
    public function user_can_view_their_service_record_form()
    {
        $user = User::factory()->create();
        $serviceRecord = ServiceRecord::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);

        $response = $this->get('/service-record');

        $response->assertStatus(200);
        $response->assertSee($serviceRecord->name);
    }

    /** @test */
    public function admin_can_update_service_record_status()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $serviceRecord = ServiceRecord::factory()->create([
            'user_id' => $user->id,
            'request_status' => 'pending'
        ]);

        $this->actingAs($admin);

        $response = $this->patch("/service-records/{$serviceRecord->id}/status", [
            'status' => 'ready'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('service_records', [
            'id' => $serviceRecord->id,
            'request_status' => 'ready'
        ]);
    }

    /** @test */
    public function creating_service_record_notifies_admin()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        
        $this->actingAs($user);

        $serviceData = [
            'name' => 'John Doe',
            'age' => 30,
            'salary' => 60000,
            'date_of_birth' => '1994-01-01',
            'job_title' => 'Software Developer',
            'place_of_birth' => 'Manila',
            'office' => 'IT Department',
            'status' => 'active',
            'date_of_service' => '2020-01-01',
            'place_of_assignment' => 'Main Office',
        ];

        $response = $this->post('/service-records', $serviceData);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
    }

    /** @test */
    public function user_can_only_see_their_latest_service_record()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $userOldRecord = ServiceRecord::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(10)
        ]);
        
        $userLatestRecord = ServiceRecord::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()
        ]);
        
        $otherUserRecord = ServiceRecord::factory()->create(['user_id' => $otherUser->id]);
        
        $this->actingAs($user);

        $response = $this->get('/service-record');

        $response->assertStatus(200);
        $response->assertSee($userLatestRecord->name);
        $response->assertDontSee($userOldRecord->name);
        $response->assertDontSee($otherUserRecord->name);
    }
}