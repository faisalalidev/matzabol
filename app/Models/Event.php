<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = [
        'image_url'
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
    public function getImageUrlAttribute()
    {
        $url = asset(Storage::url('app/' . $this->image));
        return $url;
    }
}
