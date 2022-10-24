<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchPreference extends Model
{
    protected $fillable = ['user_id', 'by_location', 'location', 'by_country', 'country', 'by_age_range', 'ethnicity', 'distance'];

    protected $hidden = ['updated_at', 'created_at', 'deleted_at', 'status', 'id', 'user_id'];

    protected $casts =
        [
            'distance' => 'string',
        ];

}
