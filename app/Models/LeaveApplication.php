<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'date_of_filing',
        'position',
        'salary',
        'type_of_leave',
        'others',
        'number_of_days',
        'inclusive_dates',
        'status',
        'user_id',
        'department',
        'inCaseVacation',
        'withinPhilippines',
        'abroad',
        'inCaseSick',
        'inHospital',
        'outPatient',
        'inCaseSpecialLeaveBenefits',
        'inCaseStudyLeave',
        'commutation',
        'cert_as_of',
        'cert_vacation',
        'cert_sick',
        'recommendation',
        'recommendation_reason',
        'approved_days_with_pay',
        'approved_days_without_pay',
        'approved_others',
        'disapproved_reason',
        'authorized_officer',
    'authorized_officer_leave_cred',
    'authorized_officer_recommendation',
        'vacation_total_earned',
        'vacation_less_this_application',
        'vacation_balance',
        'sick_total_earned',
        'sick_less_this_application',
        'sick_balance',
        'is_deleted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Attribute casting.
     *
     * @var array
     */
    protected $casts = [
        'total_earned' => 'decimal:2',
        'less_this_application' => 'decimal:2',
        'balance' => 'decimal:2',
        'vacation_total_earned' => 'decimal:2',
        'vacation_less_this_application' => 'decimal:2',
        'vacation_balance' => 'decimal:2',
        'sick_total_earned' => 'decimal:2',
        'sick_less_this_application' => 'decimal:2',
        'sick_balance' => 'decimal:2',
        'cert_as_of' => 'date',
        'action_date' => 'date',
        'inclusive_from' => 'date',
        'inclusive_to' => 'date',
        'approved_at' => 'datetime',
    ];
}
