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
        'request_status', // pending, in_progress, verified, certified, completed, deleted
        'service_record_id',
        'verified_at',
        'certified_at',
        'completed_at',
        'generated_pdf_path',
            'generated_at',
        'certified_by',
        'verification_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'certified_at' => 'datetime',
        'completed_at' => 'datetime',
        'generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRecord()
    {
        return $this->belongsTo(ServiceRecord::class);
    }

    public function certifiedBy()
    {
        return $this->belongsTo(User::class, 'certified_by');
    }
}
