<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Config;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'full_name', 'phone_number', 'dob', 'gender', 'marrital_status',
        'religion_cast', 'height', 'country', 'ethnicity', 'nationality',
        'language', 'profession', 'education', 'religion', 'my_status',
        'about_me', 'latitude', 'longitude', 'notify_new_matches',
        'notify_message', 'notify_booster', 'is_rewind', 'first_rewind_date',
        'rewind_count', 'first_boost_date', 'boost_count', 'current_city', 'current_country'
        ,'education_detail', 'password',
        'status',
        'fname',
        'lname',
        'gender_prefer',
        'is_completed_profile',
        'fb_url',
        'twitter_url',
        'insta_url',
        'profile_image',
        'is_verified',
    ];

    protected $casts =
        [
            'rewind_count' => 'int',
            'boost_count'  => 'int',
        ];

    protected $hidden = [
        'password', 'remember_token', 'deleted_at'
    ];

    protected $with = [
      'userImage',
      'user_interests',
    ];

    protected $appends = ['profile_url'];
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

    public function user_interests()
    {
       return $this->hasMany(UserInterest::class,'user_id');
    }

    public function getProfileUrlAttribute()
    {
        $url = asset(Storage::url('app/' . $this->profile_image));
        return $url;
    }

}
