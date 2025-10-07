<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'job_title',
        'start_date',
        'status',
        'sex',
        'age',
        'date_of_birth',
        'place_of_birth',
        'salary',
        'designation',
        'place_of_assignment',
    ];

    protected $table = 'employee';
}