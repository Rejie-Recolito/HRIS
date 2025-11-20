<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_employee_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('employees.store'), [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'middlename' => 'Q',
                'department' => 'IT',
                'job_title' => 'Developer',
                'start_date' => '2020-01-01',
                'status' => 'active',
                'sex' => 'Male',
                'age' => 30,
                'date_of_birth' => '1995-01-01',
                'place_of_birth' => 'Manila',
                'address' => '123 Main St',
                'salary' => 50000,
                'designation' => 'Developer',
                'place_of_assignment' => 'Main Office',
                'phone_number' => '09123456789',
                'email_address' => 'john.doe@example.com',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('employee', [
            'lastname' => 'Doe',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_update_employee_profile()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'lastname' => 'Doe',
            'firstname' => 'John',
            'middlename' => 'Q',
            'department' => 'IT',
            'job_title' => 'Developer',
            'start_date' => '2020-01-01',
            'status' => 'active',
            'sex' => 'Male',
            'age' => 30,
            'date_of_birth' => '1995-01-01',
            'place_of_birth' => 'Manila',
            'address' => '123 St',
            'salary' => 50000,
            'designation' => 'Developer',
            'place_of_assignment' => 'Main Office',
            'phone_number' => '09123456789',
            'email_address' => 'john.doe@example.com',
        ]);

        $this->actingAs($user)
            ->post(route('employees.update', $employee->id), [
                '_method' => 'PUT',
                'lastname' => 'DoeUpdated',
                'firstname' => 'John',
                'middlename' => 'Q',
                'department' => 'IT',
                'job_title' => 'Developer',
                'start_date' => '2020-01-01',
                'status' => 'active',
                'sex' => 'Male',
                'age' => 30,
                'date_of_birth' => '1995-01-01',
                'place_of_birth' => 'Manila',
                'address' => '123 St',
                'salary' => 50000,
                'designation' => 'Developer',
                'place_of_assignment' => 'Main Office',
                'phone_number' => '09123456789',
                'email_address' => 'john.doe@example.com',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('employee', [
            'id' => $employee->id,
            'lastname' => 'DoeUpdated',
        ]);
    }
}
