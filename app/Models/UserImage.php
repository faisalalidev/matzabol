<?php

namespace App\Models;

use Storage;

use Illuminate\Database\Eloquent\Model;

class UserImage extends Model
{
    protected $fillable = [
        'user_id', 'image', 'sort_order', 'status'
    ];

    /*$url = ;*/

    protected $casts =
        [
            'user_id'    => 'string',
            'sort_order' => 'string'
        ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        $url = asset(Storage::url('app/' . $this->image));
        return $url;
    }
}