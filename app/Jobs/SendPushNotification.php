<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helper;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $deviceTokens = array() , $msg,$extraPayLoadData = array();
    public function __construct($msg,$devicetoken,$extraData)
    {
        $this->deviceTokens = $devicetoken;
        $this->msg = $msg;
        $this->extraPayLoadData = $extraData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Helper\sendPushNotifications($this->msg,$this->deviceTokens,$this->extraPayLoadData);
    }
}
