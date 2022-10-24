<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileLikeDislike extends Model
{

    protected $table = 'profile_like_dislike_boost';

    protected $fillable = [
        'sender_id', 'reciever_id', 'type', 'is_boost', 'is_like'
    ];

    protected $casts =
        [
            'sender_id' => 'string',
        ];

    /*    public function user()
        {
            return $this->belongsTo('App\Models\User','reciever_id');
        }*/


}
