<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventUserMatch extends Model
{
    use softdeletes;

    protected $fillable = [
        'id',
        'user_id',
        'event_id',
        'matched_id',
    ];
}
