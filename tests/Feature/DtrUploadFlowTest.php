<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessDtrUpload;
use App\Models\DtrUpload;

it('stores uploaded file and dispatches processing job', function () {
    Storage::fake('local');
    Bus::fake();

    $user = \App\Models\User::factory()->create(['email_verified_at' => now()]);

    $csv = new \Illuminate\Http\UploadedFile(base_path('tests/Fixtures/dtr_sample.csv'), 'dtr_sample.csv', null, null, true);

    $response = $this->actingAs($user)->post(route('dtr.upload'), [
        'csv' => $csv,
    ]);

    // The controller may redirect back after storing and dispatching — accept 200 or 302
    $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));

    // Expect an upload record created
    $this->assertDatabaseHas('dtr_uploads', [
        'filename' => 'dtr_sample.csv',
        'status' => 'pending',
    ]);

    $upload = DtrUpload::first();
    expect($upload)->not->toBeNull();

    // Job was dispatched
    Bus::assertDispatched(ProcessDtrUpload::class, function ($job) use ($upload) {
        return $job->uploadId === $upload->id;
    });
});
