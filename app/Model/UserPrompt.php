<?php

namespace App\Model;

use App\Models\Prompt;
use Storage;
use Illuminate\Database\Eloquent\Model;

class UserPrompt extends Model
{
    protected $fillable = [
        'user_id',
        'prompt_id',
        'text',
        'video',
    ];

    protected $appends = ['prompt_id'];

    public function getVideoAttribute()
    {
        $url = ($this->attributes['video']) ? asset(Storage::url('app/' . $this->attributes['video'])) : null;
        return $url;
    }

    public function prompt()
    {
        return $this->belongsTo(Prompt::class,'prompt_id');
   }
    public function getPromptIdAttribute()
    {
      $prompt = Prompt::where('id', $this->attributes['prompt_id'])->first();
      return $prompt->name;
    }

}
