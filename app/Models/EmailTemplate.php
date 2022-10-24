<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class EmailTemplate extends Model
{
    use SoftDeletes;

    public $table = 'email_templates';

    protected $fillable = ['key', 'html_body', 'text_body'];

    protected $casts = [
        'key'       => 'string',
        'html_body' => 'string',
        'text_body' => 'string'
    ];

    protected $dates = ['deleted_at'];

    const SENDERID = '__SENDER_ID__';
    const RECEIVERID = '__RECEIVER_ID__';
    const SENDERNAME = '__SENDER_NAME__';
    const RECEIVERNAME = '__RECEIVER_NAME__';
    const TYPE = '__TYPE__';
    const MESSAGE = '__MESSAGE__';

    public static $EMAIL_PARAMS = [
        self::SENDERID     => 'Sender ID',
        self::SENDERNAME   => 'Sender Name',
        self::RECEIVERID   => 'Receiver ID',
        self::RECEIVERNAME => 'Receiver Name',
        self::TYPE         => 'Type',
        self::MESSAGE      => 'Message',
    ];

    const USER_REPORT = 'USER_REPORT';

    public static $TEMPLATE_NAME = [
        self::USER_REPORT => 'User Report',

    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }
}
