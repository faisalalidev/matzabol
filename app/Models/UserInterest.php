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

    protected $appends = [
        'name'
    ];
    public function interest()
    {
        return $this->hasMany(Interest::class,'id', 'interest_id');
    }

    public function getNameAttribute()
    {
        if($this->interest()->count()){ 
            $interest= Interest::where('id', $this->attributes['interest_id'])->first();
            return $interest->name;
        }
        return [];
    }
}
