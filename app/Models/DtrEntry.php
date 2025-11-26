<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtrEntry extends Model
{
    use HasFactory;

    protected $fillable = ['upload_id', 'occurred_at', 'employee', 'emp_id', 'time_in', 'time_out', 'raw'];

    protected $casts = [
        'raw' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function upload()
    {
        return $this->belongsTo(DtrUpload::class, 'upload_id');
    }
}
