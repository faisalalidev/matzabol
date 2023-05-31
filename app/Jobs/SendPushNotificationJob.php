<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $userId;
    protected $matchedID;
    protected $title;
    protected $body;

    /**
     * Create a new job instance.
     *
     * @param  int  $userId
     * @param  string  $title
     * @param  string  $body
     * @return void
     */

    public function __construct($userId, $title, $body,$matchedID)
    {
        $this->userId = $userId;
        $this->matchedID = $matchedID;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $SERVER_API_KEY = getenv('SERVER_KEY');
        $firebaseToken = UserDevice::where('user_id', $this->matchedID)->whereNotNull('device_token')->pluck('device_token')->all();
        $user = User::where('id', $this->userId)->first();
        Log::error($user);
        if (!$firebaseToken) {
            Log::error('User does not have a device token');
            return;
        }
        $data = [
            "registration_ids" => $firebaseToken,
            "data" => [
                "id" => $this->userId,
                "full_name" => $user->full_name,
                "profile_image" => $user->profile_image,
            ],
            "notification" => [
                "title" => $this->title,
                "body" => $this->body,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        if ($response) {
            return 'success';
        } else {
            return 'error';
        }
    }
}
