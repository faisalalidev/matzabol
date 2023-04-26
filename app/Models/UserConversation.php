<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConversation extends Model
{
    //
use  SoftDeletes;
    protected $fillable = [
        'id',
        'sender_id',
        'twillio_sid',
        'receiver_id',
        'name',
    ];

    protected $with=[
        'receiver'
    ];

    public function receiver()
    {
       $user =  $this->hasOne(User::class,'id','receiver_id');
       return $user;
    }
}
