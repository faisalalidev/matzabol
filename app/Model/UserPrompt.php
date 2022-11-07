<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPrompt extends Model
{
    //

    protected $fillable = [
        'user_id',
        'prompt_id',
        'text',
        'video',
    ];
}
