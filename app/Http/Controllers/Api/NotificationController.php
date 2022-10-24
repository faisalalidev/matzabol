<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Http\Requests\Api\NotificationIDRequest;
use App\Http\Requests\Api\UserIDRequest;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use Config;

class NotificationController extends ApiBaseController
{
    protected $notification;

    public function __construct(UserRepository $userService, NotificationRepository $notify)
    {
        parent::__construct($userService);
        $this->notification = $notify;
    }

    public function getAdminNotificationByUserID(UserIDRequest $request)
    {
        $params = $request->all();
        try {
            $limit = $request->limit;
            $offset = $request->offset;

            if ($limit == "") {
                $limit = Config::get('constants.limit');
            }

            if ($offset == "") {
                $offset = 0;
            }

            if ($offset == 0) $offset = 1;

            $start_limit = ($offset - 1) * $limit;
            $offset = ($start_limit < 0) ? 0 : $start_limit;

            $params['limit'] = $limit;
            $params['offset'] = $offset;
            $res = $this->notification->getGeneralMessagesByUserID($params);

            if (isset($res['data']['data'])) {
                foreach ($res['data']['data'] as $key => $value) {
                    $value['notification_id'] = $value['id'];
                    $value['id'] = strtotime($value['created_at']);
                    $value['sort_timestamp'] = strtotime($value['created_at']);
                    $value['uu_id'] = strval(strtotime($value['created_at']));
                    $value['user_id'] = (int)$value['user_id'];
                    $res['data']['data'][$key]['thread_id'] = 0;
                    if ($value['is_read'] == 0) {
                        $res['data']['data'][$key]['message_status'] = 'received_by_server';
                    } else {
                        $res['data']['data'][$key]['message_status'] = 'read_by_user';
                    }
                }
            }

            if ($res)
                //return RESTAPIHelper::response(['admin' => $res], 200, 'Success', $this->isBlocked, '', $res['pages']);
                return RESTAPIHelper::response($res['data'], 200, 'Success', $this->isBlocked, '', $res['pages']);
            else
                return RESTAPIHelper::response([], 404, 'No Record(s) Found', $this->isBlocked);
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());
        }
    }

    public function markNotificationReadByUserID(NotificationIDRequest $request)
    {
        $params = $request->all();
        //dd($params);
        try {
            $res = false;
            foreach ($params['messages_id'] as $notification_id) {
                $res = $this->notification->changeNotificationStatus($notification_id, $params['user_id'], ['is_read' => 1]);
            }
            //if ($res)
            return RESTAPIHelper::response($res, 200, 'Success', $this->isBlocked, '');
            /*else
                return RESTAPIHelper::response([], 404, 'No Record(s) Found', $this->isBlocked);*/
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());
        }
    }

}
