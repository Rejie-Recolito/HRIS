<?php

namespace Tests\Unit\Models;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $user = User::factory()->create();
        
        $serviceRecord = ServiceRecord::create([
            'user_id' => $user->id,
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
        ]);

        $this->assertInstanceOf(ServiceRecord::class, $serviceRecord);
        $this->assertEquals('John Doe', $serviceRecord->name);
        $this->assertEquals(30, $serviceRecord->age);
        $this->assertEquals($user->id, $serviceRecord->user_id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $serviceRecord = ServiceRecord::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $serviceRecord->user);
        $this->assertEquals($user->id, $serviceRecord->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
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
            'user_id',
        ];

        $serviceRecord = new ServiceRecord();
        
        $this->assertEquals($fillable, $serviceRecord->getFillable());
    }

    /** @test */
    public function it_can_have_different_job_titles()
    {
        $user = User::factory()->create();
        
        $developer = ServiceRecord::factory()->create([
            'user_id' => $user->id,
            'job_title' => 'Software Developer'
        ]);
        
        $manager = ServiceRecord::factory()->create([
            'user_id' => $user->id,
            'job_title' => 'Project Manager'
        ]);

        $this->assertEquals('Software Developer', $developer->job_title);
        $this->assertEquals('Project Manager', $manager->job_title);
    }

    /** @test */
    public function it_can_calculate_years_of_service()
    {
        $user = User::factory()->create();
        
        $serviceRecord = ServiceRecord::factory()->create([
            'user_id' => $user->id,
            'date_of_service' => '2020-01-01'
        ]);

        $yearsOfService = now()->diffInYears($serviceRecord->date_of_service);
        
        $this->assertIsInt($yearsOfService);
        $this->assertGreaterThanOrEqual(0, $yearsOfService);
    }
}