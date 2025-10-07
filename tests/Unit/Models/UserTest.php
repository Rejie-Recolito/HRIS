<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /** @test */
    public function it_has_many_leave_applications()
    {
        $user = User::factory()->create();
        $leaveApplication1 = LeaveApplication::factory()->create(['user_id' => $user->id]);
        $leaveApplication2 = LeaveApplication::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->leaveApplications);
        $this->assertCount(2, $user->leaveApplications);
        $this->assertTrue($user->leaveApplications->contains($leaveApplication1));
        $this->assertTrue($user->leaveApplications->contains($leaveApplication2));
    }

    /** @test */
    public function it_has_many_service_records()
    {
        $user = User::factory()->create();
        $serviceRecord1 = ServiceRecord::factory()->create(['user_id' => $user->id]);
        $serviceRecord2 = ServiceRecord::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->serviceRecords);
        $this->assertCount(2, $user->serviceRecords);
        $this->assertTrue($user->serviceRecords->contains($serviceRecord1));
        $this->assertTrue($user->serviceRecords->contains($serviceRecord2));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'email',
            'password',
        ];

        $user = new User();
        
        $this->assertEquals($fillable, $user->getFillable());
    }

    /** @test */
    public function it_hides_sensitive_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $user = new User();
        
        $this->assertEquals($hidden, $user->getHidden());
    }

    /** @test */
    public function password_is_hashed_when_created()
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password'
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(password_verify('plaintext-password', $user->password));
    }
}