<?php

namespace App;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Config;

class User extends Authenticatable
{
    use CanResetPassword;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','role_id','updated_at','deleted_at'
    ];
   /* public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }*/
    protected $appends = ['user_image'];
    public function getUserImageAttribute() {

        $userImage = asset(Config::get('constants.images.user') . ($this->image ?: Config::get('constants.images.default')));
        return $userImage;
    }

}
