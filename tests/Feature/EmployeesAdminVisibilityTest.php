<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeesAdminVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_employee_is_visible_to_admin()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        // User fills their employee profile
        $this->actingAs($user)
            ->post(route('employees.store'), [
                'lastname' => 'Smith',
                'firstname' => 'Anna',
                'middlename' => 'B',
                'department' => 'HR',
                'job_title' => 'HR Specialist',
                'start_date' => '2022-01-01',
                'status' => 'Active',
                'sex' => 'Female',
                'age' => 28,
                'date_of_birth' => '1997-01-01',
                'place_of_birth' => 'City',
                'address' => 'Somewhere',
                'salary' => 40000,
                'designation' => 'HR Specialist',
                'place_of_assignment' => 'Main Office',
                'phone_number' => '09111234567',
                'email_address' => 'anna.smith@example.com',
            ])
            ->assertStatus(302);

        // Admin views employees list
        $response = $this->actingAs($admin)->get(route('employees.index'));
        $response->assertStatus(200);
        $response->assertSee('Smith');
    }
}
