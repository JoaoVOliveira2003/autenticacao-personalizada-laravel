<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User3 extends Authenticatable
{
    use HasFactory;

    protected $table = 'users3';

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
