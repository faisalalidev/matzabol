<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThreadUser extends Model
{
    protected $table = 'thread_users';

    protected $casts = ['thread_id' => 'string', 'user_id' => 'string'];

}

