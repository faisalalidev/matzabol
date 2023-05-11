<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    //
    use softdeletes;

    protected $fillable = [
        'id',
        'name',
        'location',
        'day',
        'time',
        'date',
        'description',
        'image',
        'type',
        'address_latitude',
        'address_longitude',
    ];


    public function usersInfo()
    {
       return $this->hasMany(EventJoin::class,'event_id');
    }

    public function matches()
    {
        return $this->hasMany(EventUserMatch::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_users')
            ->withTimestamps();
    }
}
