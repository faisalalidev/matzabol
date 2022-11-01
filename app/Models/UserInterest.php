<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInterest extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'interest_id',
    ];
}
