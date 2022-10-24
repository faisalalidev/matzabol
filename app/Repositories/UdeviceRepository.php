<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\UserDevice;
use Config;

class UdeviceRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return UserDevice::class;
    }

    /***********************************************API***********************************************/

    /*Old Functions */

    public function getByDeviceToken($data)
    {

        return $this->model->where('device_token', $data)->get();

    }

    public function deleteByDeviceToken($data)
    {
        return $this->model->where('device_token', $data)->delete();
    }

    /**/
    public function getEnabledDeviceToken($params)
    {
        if ($params['action_type'] == Config::get('constants.notifications')['2']['title'])    // If action type is 'like'
        {
            return $this->model
                ->join('users', 'user_devices.user_id', 'users.id')
                ->where('users.notify_new_matches','=','1')
                ->where('user_id', $params['user_id'])
                ->get();

        } elseif ($params['action_type'] == Config::get('constants.notifications')['3']['title']) // if action type is 'is_boost'
        {
            return $this->model
                ->join('users', 'user_devices.user_id', 'users.id')
                ->where('users.notify_booster','1')
                ->where('user_id', $params['user_id'])
                ->get();

        } else {
            return $this->model
                ->where('user_id', $params['user_id'])
                ->get();


        }
    }

    public function updateDeviceToken($params){
        return $this->model->updateOrCreate([
            'device_token' => $params['device_token']
        ],$params);
    }

}
