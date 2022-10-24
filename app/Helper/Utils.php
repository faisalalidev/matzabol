<?php

namespace App\Helper;

use App\Mail\EmailNotification;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class Utils
{
    use Dispatchable;

    public function StartEmailJob($data)
    {
        $templateKey = EmailTemplate::USER_REPORT;
        $getTemplate = EmailTemplate::where(['key' => $templateKey])->with('users')->first();

        $subject = "Email Notification";
        if ($getTemplate) {
            $subject = str_replace('_', ' ', $getTemplate->key);
        }

        $admin = User::select('email')->where('role_id', 1)->get()->first();
        $toEmail['toEmail'] = $admin->email;

        if (!empty($getTemplate->users)) {
            $toEmail['ccEmail'] = $getTemplate->users->pluck('email')->toArray();
        }

        $keys = array_keys(EmailTemplate::$EMAIL_PARAMS);
        if($data['message'] == $data['type']) {
            $data['message'] = '';
        }
        $data['type'] = str_replace('_',' ',$data['type']);

        $values = [
            $data['sender_id'],
            $data['sender_name'],
            $data['reciever_id'],
            $data['reciever_name'],
            $data['type'],
            $data['message'],
        ];

        $htmlBody = str_replace($keys, $values, $getTemplate->html_body);
        $textBody = str_replace($keys, $values, $getTemplate->text_body);

        $templateBody = ['html' => $htmlBody, 'text' => $textBody];

        if (isset($toEmail['ccEmail'])) {
            Mail::to($toEmail['toEmail'])->cc($toEmail['ccEmail'])->queue(new EmailNotification($templateBody, $subject));
        } else {
            Mail::to($toEmail['toEmail'])->queue(new EmailNotification($templateBody, $subject));
        }
    }

}