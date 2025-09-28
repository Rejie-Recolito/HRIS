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
    ];
}