<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dropdown extends Model
{
    protected $fillable =['languages','height','ehtnicity','religion','nationality'];

    protected $hidden = ['created_at','deleted_at'];

}
