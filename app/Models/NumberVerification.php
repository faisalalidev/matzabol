<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberVerification extends Model
{
    //
    protected $fillable = [
        'phone_number', 'verification_code'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function getByNumber($number)
    {
        Model::where('phone_number', $number)
            ->get();
    }

}
