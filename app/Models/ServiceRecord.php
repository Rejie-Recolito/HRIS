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
        'employee_id',
        'user_id',
        // Service inclusive dates
        'service_from',
        'service_to',
        // Record of appointment
        'appointment_designation',
        'appointment_status',
        'appointment_salary',
        // Office entity/division
        'station_place',
        'leave_of_absence',
        // Separation
        'separation_date',
        'separation_cause',
    ];

    protected $casts = [
        'service_from' => 'date',
        'service_to' => 'date',
        'separation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}