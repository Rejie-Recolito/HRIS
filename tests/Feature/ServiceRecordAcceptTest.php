<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceRecordRequest;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRecordAcceptTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_accept_request_and_service_record_is_created()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        // create a request
        $req = ServiceRecordRequest::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'request_status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('service-record-requests.accept', $req->id))
            ->assertStatus(302);

        // verify service record was created and request is in_progress
        $this->assertDatabaseHas('service_records', [
            'user_id' => $user->id,
            'name' => $user->name,
        ]);

        $req->refresh();
        $this->assertEquals('in_progress', $req->request_status);
        $this->assertNotNull($req->service_record_id);
    }
}
