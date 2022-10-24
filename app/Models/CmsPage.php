<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'body', 'status', 'updated_at'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
