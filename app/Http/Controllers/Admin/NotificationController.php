<?php

namespace App\Http\Controllers\Admin;

use function App\Helper\recursive_array_search;
use function App\Helper\sendPushNotifications;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Repositories\NotificationRepository;
use App\Repositories\UdeviceRepository;
use App\Repositories\UserRepository;
use App\Jobs\SendPushNotification;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Alert;
use Auth;
use Config;
use DB;

class NotificationController extends Controller
{

    protected $notification, $uDevices, $users;

    public function __construct(NotificationRepository $notification, UdeviceRepository $uDevice, UserRepository $user)
    {
        $this->notification = $notification;
        $this->uDevices = $uDevice;
        $this->users = $user;
    }

    public function create()
    {
        $users = $this->users->getUserByDevice('ios');
        return view('admin.notifications.add_notification', ['module' => 'Notifications', 'users' => $users]);
    }

    public function store(SendNotificationRequest $request)
    {

        $notificationData = [];
        $pushNotificationData = [];
        $id = Auth::id();

        $notificationData['message'] = $request->message;
        $notificationData['sender_id'] = $id;
        $notificationData['action_type'] = Config::get('constants.notifications')['1']['title']; // 1 is for general
        $notificationData['users'] = array();

        foreach ($request['users'] as $key => $value):
            $value = json_decode($value, true);

            if (!in_array($value['id'], $notificationData['users'])) // distinct user's id
                $notificationData['users'][$key] = $value['id'];

            // TODO: Add Badge Count
            /*Get Unread Count*/
            $unreadLikeCount = $this->notification->getUnReadCount($value['id'], $action_type = "like");
            $unreadBoostCount = $this->notification->getUnReadCount($value['id'], $action_type = "is_boost");
            $unreadAdminNotiCount = $this->notification->getUnReadCount($value['id'], $action_type = "general");
            // FIXME: Use Models and Repositories to replace below query.
            $res = DB::select('SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=?) OR (ur.sender_id=c.sender_id AND reciever_id=?) WHERE cr.type <> \'read_by_user\' AND c.sender_id <> ? AND tu.user_id = ? AND c.deleted_at IS NULL AND (c.deleted_by <> ? OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))', [$value['id'], $value['id'], $value['id'], $value['id'], $value['id']]);

            $unreadMsgCount = $res ? $res[0]->unread_messages : 0;
            // TODO: Plus one to include this notification.
            $badge = $unreadLikeCount + $unreadBoostCount + $unreadMsgCount + $unreadAdminNotiCount + 1;


            //$notificationData['users'][$key] = $value['id'];
            if ($request->device_type == 'all') {
                $pushNotificationData[] = $value;
            } else {
                $pushNotificationData[] = ['user_id' => $value['id'], 'device_type' => $value['device_type'], 'device_token' => $value['device_token'], 'badge_count' => $badge];
            }
        endforeach;


        /*Seed Notification Table*/
        $notification = $this->notification->setData($notificationData);
        $notification = $notification->toArray();
//        dd($notification->created_at);
        $extraPayLoadData['id'] = strtotime($notification['created_at']);
        $extraPayLoadData['created_at'] = $notification['created_at'];
        $extraPayLoadData['action_type'] = 'general';
        $extraPayLoadData['user_id'] = $id;
        $extraPayLoadData['thread_id'] = 0;
        $extraPayLoadData['message'] = $request->message;;
        $extraPayLoadData['message_status'] = 'received_by_server';
        /* "username": "Tester" */
        //dd($extraPayLoadData);

        /******************Queues push notifications jobs ***************************/
        # $jobs = (new SendPushNotification($request->message, $pushNotificationData));
        # $this->dispatch($jobs);

        // TODO: Add Badge Count
        /*Get Unread Count*/
        /*$unreadLikeCount = $this->notification->getUnReadCount($id, $action_type = "like");
        $unreadBoostCount = $this->notification->getUnReadCount($id, $action_type = "is_boost");
        $unreadAdminNotiCount = $this->notification->getUnReadCount($id, $action_type = "general");
        // FIXME: Use Models and Repositories to replace below query.
        $res = DB::select('SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=?) OR (ur.sender_id=c.sender_id AND reciever_id=?) WHERE cr.type <> \'read_by_user\' AND c.sender_id <> ? AND tu.user_id = ? AND c.deleted_at IS NULL AND (c.deleted_by <> ? OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))', [$id, $id, $id, $id, $id]);

        $unreadMsgCount = $res ? $res[0]->unread_messages : 0;
        $badge = $unreadLikeCount + $unreadBoostCount + $unreadMsgCount + $unreadAdminNotiCount;*/


        sendPushNotifications($request->message, $pushNotificationData, $extraPayLoadData, 0);
        Alert::success('Send Successfully');
        return Redirect::back();

    }

    public function getUsersByType($type)
    {
        $user = $this->users->getUserByDevice($type);
        return $user;
    }

}
