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
    ];
}
