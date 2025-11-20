<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
        // New fields for admin-filled service record details
        'service_from',
        'service_to',
        'appointment_rank',
        'appointment_designation',
        'appointment_status',
        'appointment_monthly_base_pay',
        'station',
        'place',
    'leave_of_absence',
        'separation_date',
        'separation_cause',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}