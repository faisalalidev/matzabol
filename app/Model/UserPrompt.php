<?php

namespace App\Model;

use Storage;
use Illuminate\Database\Eloquent\Model;

class UserPrompt extends Model
{
    protected $fillable = [
        'user_id',
        'prompt_id',
        'text',
        'video',
    ];

//    protected $appends = ['video'];

    public function getVideoAttribute()
    {
        $url = ($this->attributes['video']) ? asset(Storage::url('app/' . $this->attributes['video'])) : null;
        return $url;
    }
}
