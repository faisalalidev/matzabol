<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationRepository extends BaseRepository
{

    public function model()
    {
        return Notification::class;
    }

    public function getByReceiverId($receiver_id)
    {
        return $this->model->select('id as notification_id', 'receiver_id', 'text', 'action_type', 'read', 'ref_id', 'created_at')->where('receiver_id', $receiver_id)->get();
    }

    public function setData($data)
    {
        $noti = $this->model->create($data);
        $noti->users()->attach($data['users']);
        return $noti;
    }

    public function getUnsentNotifications()
    {
        $id = Auth::id();
        $data = $this->model->select('users.email', 'notifications.message', 'notifications.url', 'notifications.added_by', 'notification_user.notification_id', 'notification_user.user_id')
            ->join('notification_user', function ($join) {
                $join->on('notifications.id', '=', 'notification_user.notification_id')
                    ->where('notification_user.is_sent', '=', 0);
            })
            ->join('users', function ($join) {
                $join->on('notification_user.receiver_id', '=', 'users.id');
            })
            ->where('notifications.sender_id', '=', $id)
            //->where('notification_user.is_sent', '=', 0)
            ->get();
        return $data;
    }

    public function changeNotificationStatus($notification_id, $user_id, $attributes)
    {
        return $this->model->find($notification_id)->users()->updateExistingPivot($user_id, $attributes);
    }

    public function getUnReadCount($user_id, $action_type)
    {
        return $this->model
            ->join('notification_user', 'notification_user.notification_id', '=', 'notifications.id')
            ->where([
                ['notification_user.user_id', $user_id],
                ['notification_user.is_read', 0],
                ['notifications.action_type', $action_type],
            ])
            ->count();
    }


    public function getGeneralMessagesByUserID($params)
    {

        $res = array();

        $unread_messages = $this->getUnReadCount($params['user_id'], 'general');

        $count = $this->model->select(['notifications.id', 'message', 'ref_id', 'action_type', 'notifications.created_at', 'users.id AS user_id', 'users.full_name AS username', 'notification_user.is_sent', 'notification_user.is_read'])
            ->join('notification_user', 'notification_user.notification_id', 'notifications.id')
            ->join('users', 'users.id', 'notifications.sender_id')
            ->where([
                ['notification_user.user_id', $params['user_id']],
                ['notifications.action_type', 'general']
            ])
            ->groupBy('notifications.id')
            ->count();

        if ($count > 0) {
            $res['data']['data'] = $this->model->select(['notifications.id', 'message', 'ref_id', 'action_type', 'notifications.created_at', 'users.id AS user_id', 'users.full_name AS username', 'notification_user.is_sent', 'notification_user.is_read'])
                //->selectRaw('uuid() AS uu_id')
                ->join('notification_user', 'notification_user.notification_id', 'notifications.id')
                ->join('users', 'users.id', 'notifications.sender_id')
                ->where([
                    ['notification_user.user_id', $params['user_id']],
                    ['notifications.action_type', 'general']
                ])
                ->limit($params['limit'])
                ->offset($params['offset'])
                ->orderBy('id', 'desc')
                ->groupBy('notifications.id')
                ->get();

            $res['pages'] = ceil($count / $params['limit']);
        } else {
            $res['pages'] = 0;
        }

        $res['data']['unread_messages'] = $unread_messages;

        return $res;

    }

}
