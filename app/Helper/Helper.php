<?php

namespace App\Helper;

use Edujugon\PushNotification\PushNotification;
use Config;
use Mockery\Exception;

function sendPushNotifications($msg = 'Veil', $deviceObject = [], $extraPayLoadData = [], $badge = 0)
{
    try {

        $deviceTokenAndroid = [];
        $deviceTokenIphone = [];

        foreach ($deviceObject as $device):
            if ($device['device_type'] == 'android') {
                //$deviceTokenAndroid[] = $device['device_token'];
                $push = new PushNotification('fcm');
                $push->setMessage([
                    'title'   => config('app.name'),
                    'message' => $msg

                ])
                    ->setApiKey(Config::get('constants.pushNotification.fcm'))
                    ->setConfig(['dry_run' => false])
                    ->setDevicesToken($device['device_token'])
                    ->send();
            } else {
                //$deviceTokenIphone[] = $device['device_token'];

                $push = new PushNotification('apn');

                if ($extraPayLoadData['action_type'] == "general") {
                    $push->setMessage([
                        'aps'          => [
                            'alert' => [
                                'title' => config('app.name'),
                                'body'  => $msg,
                            ],
                            'sound' => 'default',
                            'badge' => $device['badge_count']
                        ],
                        'extraPayLoad' => [
                            'action_type'    => $extraPayLoadData['action_type'],
                            'action_id'      => $extraPayLoadData['thread_id'],
                            'user_id'        => $extraPayLoadData['user_id'],
                            'id'             => $extraPayLoadData['id'],
                            'created_at'     => $extraPayLoadData['created_at'],
                            'message'        => $extraPayLoadData['message'],
                            'message_status' => $extraPayLoadData['message_status'],
                            'thread_id'      => $extraPayLoadData['thread_id'],

                        ]
                    ])
                        ->setDevicesToken($device['device_token'])
                        ->send();
                } else {
                    $push->setMessage([
                        'aps'          => [
                            'alert' => [
                                'title' => config('app.name'),
                                'body'  => $msg,
                            ],
                            'sound' => 'default',
                            'badge' => $device['badge_count'],
                        ],
                        'extraPayLoad' => [
                            'action_type' => $extraPayLoadData['action_type'],
                            'action_id'   => $extraPayLoadData['thread_id'],
                            'user_id'     => $extraPayLoadData['user_id']

                        ]
                    ])
                        ->setDevicesToken($device['device_token'])
                        ->send();
                }

            }
        endforeach;

        /*if ($deviceTokenAndroid) {
            $push = new PushNotification('fcm');
            $push->setMessage([
                'title'   => config('app.name'),
                'message' => $msg

            ])
                ->setApiKey(Config::get('constants.pushNotification.fcm'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($deviceTokenAndroid)
                ->send();
        }*/

        /*Apn*/
        /*if ($deviceTokenIphone) {
            $push = new PushNotification('apn');

            if ($extraPayLoadData['action_type'] == "general") {
                $push->setMessage([
                    'aps'          => [
                        'alert' => [
                            'title' => config('app.name'),
                            'body'  => $msg,
                        ],
                        'sound' => 'default',
                        'badge' => $badge
                    ],
                    'extraPayLoad' => [
                        'action_type'    => $extraPayLoadData['action_type'],
                        'action_id'      => $extraPayLoadData['thread_id'],
                        'user_id'        => $extraPayLoadData['user_id'],
                        'id'             => $extraPayLoadData['id'],
                        'created_at'     => $extraPayLoadData['created_at'],
                        'message'        => $extraPayLoadData['message'],
                        'message_status' => $extraPayLoadData['message_status'],
                        'thread_id'      => $extraPayLoadData['thread_id'],

                    ]
                ])
                    ->setDevicesToken($deviceTokenIphone)
                    ->send();
            } else {
                $push->setMessage([
                    'aps'          => [
                        'alert' => [
                            'title' => config('app.name'),
                            'body'  => $msg,
                        ],
                        'sound' => 'default',
                        'badge' => $badge,
                    ],
                    'extraPayLoad' => [
                        'action_type' => $extraPayLoadData['action_type'],
                        'action_id'   => $extraPayLoadData['thread_id'],
                        'user_id'     => $extraPayLoadData['user_id']

                    ]
                ])
                    ->setDevicesToken($deviceTokenIphone)
                    ->send();
            }

        }*/
        return true;
    } catch (Exception $e) {
        dd($e->getTraceAsString());
    }

}

function recursive_array_search($needle, $haystack)
{
    foreach ($haystack as $key => $value) {
        $current_key = $key;
        if ($needle === $value OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

