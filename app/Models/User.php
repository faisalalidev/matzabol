<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Config;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'full_name', 'phone_number', 'dob', 'gender', 'marrital_status',
        'religion_cast', 'height', 'country', 'ethnicity', 'nationality',
        'language', 'profession', 'education', 'religion', 'my_status',
        'about_me', 'latitude', 'longitude', 'notify_new_matches',
        'notify_message', 'notify_booster', 'is_rewind', 'first_rewind_date',
        'rewind_count', 'first_boost_date', 'boost_count', 'current_city', 'current_country'
        , 'education_detail', 'password',
        'fname',
        'lname',
    ];

    protected $casts =
        [
            'rewind_count' => 'int',
            'boost_count'  => 'int',
        ];

    protected $hidden = [
        'password', 'remember_token', 'deleted_at'
    ];

    public function userImage()
    {
        return $this->hasMany('App\Models\UserImage');
    }

    public function userDevice()
    {
        return $this->hasMany('App\Models\UserDevice');
    }

    public function actionType()
    {
        return $this->hasOne('App\Models\ProfileLikeDislike', 'sender_id');
    }

    public function searchPreference()
    {
        return $this->hasOne('App\Models\SearchPreference');
    }

    public function notifications()
    {
        return $this->belongsToMany('App\Models\Notification')->withPivot('is_read');
    }


}
