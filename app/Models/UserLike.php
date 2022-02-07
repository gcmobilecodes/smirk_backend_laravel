<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLike extends Model
{

    use HasFactory;

    protected $fillable = [
        'userid',
        'like',
        'status',
    ];

}
