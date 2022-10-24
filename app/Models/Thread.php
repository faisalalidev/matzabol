<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = ['created_by','type'];

    public function users(){
        return $this->belongsToMany('App\Models\User','thread_users');
    }


}
