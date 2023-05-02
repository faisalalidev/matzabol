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
    ];


    public function users()
    {
       return $this->hasMany(EventJoin::class,'event_id');
    }
}
