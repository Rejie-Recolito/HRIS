<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRecordRequest extends Model
{
    use HasFactory;

    protected $table = 'service_record_requests';

    protected $fillable = [
        'user_id',
        'name',
        'request_status', // pending, in_progress, accepted, deleted
        'service_record_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRecord()
    {
        return $this->belongsTo(ServiceRecord::class);
    }
}
