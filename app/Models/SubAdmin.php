<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class SubAdmin extends Model
{
    use SoftDeletes;

    protected $fillable = ['full_name', 'email', 'password', 'phone_number', 'role_id', 'status', 'remember_token'];

    protected $table = 'users';

    protected $hidden = [
        'password', 'remember_token', 'deleted_at'
    ];

}
