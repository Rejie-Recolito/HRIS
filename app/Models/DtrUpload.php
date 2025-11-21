<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtrUpload extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'filename', 'path', 'size', 'status', 'error'];

    public function entries()
    {
        return $this->hasMany(DtrEntry::class, 'upload_id');
    }
}
