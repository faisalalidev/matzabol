<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\User;
use DB;
use Config;

class UserRepository extends BaseRepository
{


    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /****************************************web********************************************************/
    /****************************************web********************************************************/
    /****************************************web********************************************************/

    public function updateStatus($status, $id)
    {
        $model = $this->model->find($id);
        $model->status = $status;
        $model->update();
        return $model;
    }

    public function getDetailsById($id)
    {
        return $this->model->select('*')
            ->selectRaw('(SELECT COUNT(id) FROM profile_like_dislike_boost WHERE sender_id = ? AND (is_boost = "1" OR type = "boost")) AS total_boost', [$id])
            ->where('id', $id)->with('userImage')
            ->first();
    }

    public function getUserLikesProfiles($user_id)
    {
        return $this->model->select('users.id', 'users.full_name', 'profile_like_dislike_boost.created_at')
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = users.id AND sender_id = ?) AS reported_to', [$user_id])
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = ? AND sender_id = users.id) AS reported_by', [$user_id])
            ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.reciever_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.sender_id', '=', $user_id)
            ->where('profile_like_dislike_boost.type', '=', 'like')
            ->orderBy('created_at', 'desc')
            ->groupBy('users.id')
            ->get();
    }

    public function getUserDislikesProfiles($user_id)
    {
        return $this->model->select('users.id', 'users.full_name', 'profile_like_dislike_boost.created_at')
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = users.id AND sender_id = ?) AS reported_to', [$user_id])
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = ? AND sender_id = users.id) AS reported_by', [$user_id])
            ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.reciever_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.sender_id', '=', $user_id)
            ->where('profile_like_dislike_boost.type', '=', 'dislike')
            ->orderBy('created_at', 'desc')
            ->groupBy('users.id')
            ->get();
    }

    public function getUserReportedByProfiles($user_id)
    {
        return $this->model->select('users.id', 'users.full_name', 'user_reports.type', 'user_reports.message', 'user_reports.created_at')
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = users.id AND sender_id = ?) AS reported_to', [$user_id])
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = ? AND sender_id = users.id) AS reported_by', [$user_id])
            ->join('user_reports', 'user_reports.sender_id', '=', 'users.id')
            ->where('user_reports.reciever_id', '=', $user_id)
            ->orderBy('created_at', 'desc')
            ->groupBy('users.id')
            ->get();
    }

    public function getUserBoostProfiles($user_id)
    {
        return $this->model->select('users.id', 'users.full_name', 'profile_like_dislike_boost.created_at')
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = users.id AND sender_id = ?) AS reported_to', [$user_id])
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = ? AND sender_id = users.id) AS reported_by', [$user_id])
            ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.reciever_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.sender_id', '=', $user_id)
            ->where('profile_like_dislike_boost.type', '=', 'boost')
            ->orderBy('created_at', 'desc')
            ->groupBy('users.id')
            ->get();
    }

    public function getUserThreads($user_id)
    {
        return $this->model->select('users.id', 'users.full_name', 'th.id AS thread_id', 'th.type as threadType', 'th.created_at')
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = users.id AND sender_id = ?) AS reported_to', [$user_id])
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = ? AND sender_id = users.id) AS reported_by', [$user_id])
            ->join('thread_users AS thus2', 'users.id', 'thus2.user_id')
            ->join('threads AS th', 'th.id', 'thus2.thread_id')
            ->whereIn('thus2.thread_id', function ($query) use ($user_id) {
                $query->select('th.id')->from('thread_users AS thus')->join('threads AS th', 'thus.thread_id', 'th.id')->where('thus.user_id', $user_id);
            })
            ->where('users.id', '!=', $user_id)
            ->groupBy('th.id')
            ->orderBy('th.created_at', 'DESC')
            ->get();
    }

    public function getUserByDevice($type)
    {
        if ($type == "all") {
            $data = $this->model->select('users.id', 'users.full_name', 'user_devices.device_token', 'user_devices.device_type')
                ->leftJoin('user_devices',
                    function ($join) {
                        $join->on('users.id', '=', 'user_devices.user_id');
                    })
                ->where('users.status', '=', '1')
                ->whereNotIn('users.role_id', [1])
                ->orderBy('users.full_name', 'ASC')
                ->get();
        } else {
            $data = $this->model->select('users.id', 'users.full_name', 'user_devices.device_token', 'user_devices.device_type')
                ->leftJoin('user_devices',
                    function ($join) {
                        $join->on('users.id', '=', 'user_devices.user_id');
                    })
                //->where('user_devices.device_type', '=', $type)
                ->where('users.status', '=', '1')
                ->whereNotIn('users.role_id', [1])
                ->orderBy('users.full_name', 'ASC')
                ->get();
        }
        return $data;
    }

    public function getAdminEmail()
    {
        return $this->model->select('email')->where('role_id', '=', 1)->where('status', '=', '1')->first();
    }

    public function getSubAdminEmail()
    {
        return $this->model->select('email')->where('role_id', '=', 3)->where('status', '=', '1')->get()->toArray();
    }

    public function getNameByID($id)
    {
        return $this->model->select('full_name')->where('id', '=', $id)->first();
    }

    public function getSubAdmin()
    {
        return $this->model->where('role_id', '=', 3)->where('status', '=', '1')->pluck('full_name', 'id')->all();
    }

    /***********************************************API***********************************************/
    /***********************************************API***********************************************/
    /***********************************************API***********************************************/


    /*Old Functions Addy*/

    public function setDataWithImages($data)
    {
        return $this->model->create($data)->with('');
    }

    public function getDataTable()
    {
        return $this->model->select('users.*')
            ->selectRaw('(SELECT IF (COUNT(reciever_id) > 0, TRUE, FALSE) FROM user_reports WHERE reciever_id = users.id) AS reported')
            ->selectRaw('(SELECT COUNT(id) FROM profile_like_dislike_boost WHERE sender_id = users.id AND (is_boost = "1" OR type = "boost")) AS total_boost')
            ->where('role_id', 2)
            ->orderBy('users.created_at', 'ASC')
            ->get();
    }

    public function getUserStatus($id)
    {   $user = $this->model->
        select('status')
        ->where('id', $id)->first();
        return $user->status;
    }

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getByNumber($number)
    {
        return $this->model->where('phone_number', $number)->with(['userImage', 'searchPreference'])->first();
    }

    public function getByIdWithImages($id)
    {
        return $this->model->where('id', $id)->with(['userImage', 'searchPreference'])->first();
    }

    public function getSearchProfiles($params)
    {
        $circle_radius = Config::get('constants.radius_circle');
        #  $max_distance = 20;

        $res = [];
        $preference = $params['user_preference'];
        if (!$preference) {
            $preference['by_country'] = 0;
            $preference['by_age_range'] = 0;
            $preference['by_ethnicity'] = 0;
            $preference['by_location'] = 1;
            $preference['distance'] = 20;
            $preference = (object)$preference;
        }

        if ($params['offset'] == "")
            $params['offset'] = 0;

        if ($params['offset'] == 0) $params['offset'] = 1;

        $start_limit = ($params['offset'] - 1) * $params['limit'];
        $params['offset'] = ($start_limit < 0) ? 0 : $start_limit;
        // Get count

        $resCount = $this->model
            ->select('users.*')
            ->selectRaw('( ? * acos( cos( radians(?) ) *
                               cos( radians( latitude ) )
                               * cos( radians( longitude ) - radians(?)
                               ) + sin( radians(?) ) *
                               sin( radians( latitude ) ) )
                             ) AS distance
                             ', [$circle_radius, $params['latitude'], $params['longitude'], $params['latitude']])
            ->where('users.id', '<>', $params['user_id'])
            ->whereNotIn('users.id', function ($query) use ($params) {
                $query->select('profile_like_dislike_boost.reciever_id')
                    ->from('profile_like_dislike_boost')
                    ->where('profile_like_dislike_boost.sender_id', '=', $params['user_id']);
            })
            // Ignore Reported Users
            ->whereNotIn('users.id', function ($query) use ($params) {
                $query->select('user_reports.reciever_id')
                    ->from('user_reports')
                    ->where('user_reports.sender_id', '=', $params['user_id']);
            })
            ->where('users.gender', '<>', $params['user']['gender'])
            ->where('users.status', '=', '1')
            ->where('users.role_id', '=', '2')
            ->when($preference->by_country, function ($query) use ($preference) {
                $country = explode(",", $preference->country);
                return $query->whereIn('users.current_country', $country);
            })
            ->when($preference->by_location, function ($query) use ($preference) {
                if ($preference->distance < 500) {
                    return $query->havingRaw("distance < ?", [$preference->distance]);
                }
                return $query;
            })
            ->when($preference->by_age_range, function ($query) use ($preference) {
                $range = explode("-", $preference->age_range);
                $min = (int)$range[0];
                $max = (int)$range[1];
                return $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(users.dob,"%M %d %Y")) BETWEEN ' . $min . ' AND ' . $max . '');
            })
            ->when($preference->by_ethnicity, function ($query) use ($preference) {
                return $query->where('users.ethnicity', $preference->ethnicity);
            })
//            ->toSql();
            ->get();
//        dd($resCount);
        $resCount = $resCount->count();

//dd($resCount);
        if ($resCount > 0) {

            /*Result Set*/
            $res['data'] = $this->model
                ->select('users.*')
                ->selectRaw('( ? * acos( cos( radians(?) ) *
                               cos( radians( latitude ) )
                               * cos( radians( longitude ) - radians(?)
                               ) + sin( radians(?) ) *
                               sin( radians( latitude ) ) )
                             ) AS distance
                             ', [$circle_radius, $params['latitude'], $params['longitude'], $params['latitude']])
                ->where('users.id', '<>', $params['user_id'])
                ->whereNotIn('users.id', function ($query) use ($params) {
                    $query->select('profile_like_dislike_boost.reciever_id')
                        ->from('profile_like_dislike_boost')
                        ->where('profile_like_dislike_boost.sender_id', '=', $params['user_id']);
                })
                // Ignore Reported Users
                ->whereNotIn('users.id', function ($query) use ($params) {
                    $query->select('user_reports.reciever_id')
                        ->from('user_reports')
                        ->where('user_reports.sender_id', '=', $params['user_id']);
                })
                ->where('users.gender', '<>', $params['user']['gender'])
                ->where('users.status', '=', '1')
                ->where('users.role_id', '=', '2')
                ->when($preference->by_country, function ($query) use ($preference) {
                    $country = explode(",", $preference->country);
                    return $query->whereIn('users.current_country', $country);
                })
                ->when($preference->by_location, function ($query) use ($preference) {
                    if ($preference->distance < 500) {
                        return $query->havingRaw("distance < ?", [$preference->distance]);
                    }
                    return $query;
                })
                ->when($preference->by_age_range, function ($query) use ($preference) {
                    $range = explode("-", $preference->age_range);
                    $min = (int)$range[0];
                    $max = (int)$range[1];
                    return $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(users.dob,"%M %d %Y")) BETWEEN ' . $min . ' AND ' . $max . '');
                })
                ->when($preference->by_ethnicity, function ($query) use ($preference) {
                    return $query->where('users.ethnicity', $preference->ethnicity);
                })
                ->limit($params['limit'])
                ->offset($params['offset'])
                ->with(['actionType' => function ($q) use ($params) {
                    $q->select('sender_id', 'type')
                        ->where('reciever_id', $params['user_id'])->first();
                }, 'userImage'])
//                ->toSql();
                ->get();

            $res['pages'] = ceil($resCount / $params['limit']);
        }
        return $res;

    }

    /*new functions by Max*/

    //Get I have liked or boosted profiles
    public function getILikedBoostedProfiles($params)
    {
        $res = [];
        $resCount = $this->model
            ->select('users.*', 'profile_like_dislike_boost.created_at AS liked_date')
            ->selectRaw('(SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE reciever_id=users.id AND sender_id=' . $params['user_id'] . ') as reported')
            ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.reciever_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.sender_id', '=', $params['user_id'])
            ->where(function ($query) {
                $query->where('profile_like_dislike_boost.type', '=', 'like')
                    ->orWhere('profile_like_dislike_boost.type', '=', 'boost');
            })
            ->where('users.status', '=', '1')
            ->having('reported', '=', 'FALSE')
            ->orderBy('updated_at', 'desc')
            ->groupBy('users.id')
            ->get();
//            ->count();

        if ($resCount->count() > 0) {

            $res['data'] = $this->model
                ->select('users.*', 'profile_like_dislike_boost.created_at AS liked_date', 'profile_like_dislike_boost.type', 'profile_like_dislike_boost.is_like', 'profile_like_dislike_boost.is_boost')
                /*->selectRaw('(SELECT IF((SELECT profile_like_dislike_boost.id FROM profile_like_dislike_boost
                 WHERE ( (profile_like_dislike_boost.reciever_id= '. $params['user_id'] .' OR profile_like_dislike_boost.sender_id='. $params['user_id'] .')
                  AND (profile_like_dislike_boost.reciever_id=users.id OR profile_like_dislike_boost.`sender_id`=users.id) 
                  AND profile_like_dislike_boost.type="like") LIMIT 1),1,0) )
                 AS is_match')*/
                ->selectRaw('(SELECT IF (COUNT(id) = 2, 1, 0) AS is_matched FROM profile_like_dislike_boost pldb WHERE 
        	    (pldb.type = "like" OR pldb.type = "boost") AND ((pldb.reciever_id = ' . $params['user_id'] . ' AND pldb.sender_id = users.id) OR (pldb.reciever_id = users.id AND pldb.sender_id = ' . $params['user_id'] . ')))
        	    as is_match')
                ->selectRaw('(SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE (reciever_id=users.id AND sender_id=' . $params['user_id'] . ') OR (sender_id=users.id AND reciever_id=' . $params['user_id'] . ')) as reported')
                ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.reciever_id', '=', 'users.id')
                ->where('profile_like_dislike_boost.sender_id', '=', $params['user_id'])
                ->where(function ($query) {
                    $query->where('profile_like_dislike_boost.type', '=', 'like')
                        ->orWhere('profile_like_dislike_boost.type', '=', 'boost');
                })
                //->with('userImage') change by SAG
                ->with(['actionType' => function ($q) {
                    $q->select('sender_id', 'type');
                }, 'userImage'])
                ->where('users.status', '=', '1')
                ->having('reported', '=', 'FALSE')
                ->limit($params['limit'])
                ->offset($params['offset'])
                ->orderBy('updated_at', 'desc')
                ->groupBy('users.id')
                ->get();


            $res['pages'] = ceil($resCount->count() / $params['limit']);

        }

        return $res;

    }

    //Get boosted profiles
    public function getBoostersByUserID($params)
    {
        $res = [];

        /*
         * SELECT *, (
         * 		SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE reciever_id=users.id AND sender_id=121
         * ) AS reported
         * FROM users
         * JOIN profile_like_dislike_boost ON profile_like_dislike_boost.sender_id=users.id
         * WHERE profile_like_dislike_boost.reciever_id = 121
         * AND profile_like_dislike_boost.type = 'boost'
         * AND users.status = '1'
         * HAVING reported = FALSE
         *
         * */

        $resCount = $this->model
            ->select('users.*')
            ->selectRaw('(SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE reciever_id=users.id AND sender_id=' . $params['user_id'] . ') as reported')
            ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.sender_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.reciever_id', '=', $params['user_id'])
            //->where('profile_like_dislike_boost.type', '=', 'boost')
            ->where('profile_like_dislike_boost.is_boost', '=', '1')
            ->where('users.status', '=', '1')
            ->having('reported', '=', 'FALSE')->get();
//            ->count();
//        dd($resCount->count());
        if ($resCount->count() > 0) {

            $res['data'] = $this->model
                ->select('users.*', 'profile_like_dislike_boost.created_at AS liked_date', 'profile_like_dislike_boost.id as ref_id', 'profile_like_dislike_boost.type', 'profile_like_dislike_boost.is_like', 'profile_like_dislike_boost.is_boost')
                ->selectRaw('(SELECT IF (COUNT(id) = 2, 1, 0) AS is_matched FROM profile_like_dislike_boost pldb WHERE 
        	    (pldb.type = "like" OR pldb.type = "boost") AND ((pldb.reciever_id = ' . $params['user_id'] . ' AND pldb.sender_id = users.id) OR (pldb.reciever_id = users.id AND pldb.sender_id = ' . $params['user_id'] . ')))
        	    as is_match')
                ->selectRaw('(SELECT IF (COUNT(id) = 1, 1, 0) AS i_liked FROM profile_like_dislike_boost pldb WHERE pldb.is_like = "1" AND (pldb.reciever_id = users.id AND pldb.sender_id = ' . $params['user_id'] . ') ) AS i_like')
                ->selectRaw('(SELECT IF (COUNT(id) = 1, 1, 0) AS i_boosted FROM profile_like_dislike_boost pldb WHERE pldb.is_boost = "1" AND (pldb.reciever_id = users.id AND pldb.sender_id = ' . $params['user_id'] . ') ) AS i_boost')
                ->selectRaw('(SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE reciever_id=users.id AND sender_id=' . $params['user_id'] . ') as reported')
                ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.sender_id', '=', 'users.id')
                ->where('profile_like_dislike_boost.reciever_id', '=', $params['user_id'])
                //->where('profile_like_dislike_boost.type', '=', 'boost')
                ->where('profile_like_dislike_boost.is_boost', '=', '1')
                ->where('users.status', '=', '1')
                //->with('userImage') change by S.A.G
                ->with(['actionType' => function ($q) {
                    $q->select('sender_id', 'type');
                }, 'userImage'])
                ->having('reported', '=', 'FALSE')
                ->limit($params['limit'])
                ->offset($params['offset'])
                ->get();


            $res['pages'] = ceil($resCount->count() / $params['limit']);

        }

        return $res;

    }

    /*Get profiles who liked user(by id)*/
    public function profilesWhoLiked($params)
    {
        $res = [];

        $resCount = $this->model
            ->select('users.*')
            ->selectRaw('(SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE reciever_id=users.id AND sender_id=' . $params['user_id'] . ') as reported')
            ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.sender_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.reciever_id', '=', $params['user_id'])
            ->where('profile_like_dislike_boost.type', '=', 'like')
            ->where('users.status', '=', '1')
            ->having('reported', '=', 'FALSE')->get();
//            ->count();
        if ($resCount->count() > 0) {

            $res['data'] = $this->model
                ->select('users.*', 'profile_like_dislike_boost.created_at AS liked_date', 'profile_like_dislike_boost.type')
                ->selectRaw('(SELECT IF (COUNT(id) = 2, 1, 0) AS is_matched FROM profile_like_dislike_boost pldb WHERE 
        	    (pldb.type = "like" OR pldb.type = "boost") AND ((pldb.reciever_id = ' . $params['user_id'] . ' AND pldb.sender_id = users.id) OR (pldb.reciever_id = users.id AND pldb.sender_id = ' . $params['user_id'] . ')))
        	    as is_match')
                ->selectRaw('(SELECT IF(COUNT(id)>0, TRUE, FALSE) FROM user_reports WHERE reciever_id=users.id AND sender_id=' . $params['user_id'] . ') as reported')
                ->join('profile_like_dislike_boost', 'profile_like_dislike_boost.sender_id', '=', 'users.id')
                ->where('profile_like_dislike_boost.reciever_id', '=', $params['user_id'])
                #->where('profile_like_dislike_boost.type', '=', 'like')
                ->where('users.status', '=', '1')
                //->with('userImage') change by SAG
                ->with(['actionType' => function ($q) {
                    $q->select('sender_id', 'type');
                }, 'userImage'])
                ->having('reported', '=', 'FALSE')
                ->limit($params['limit'])
                ->offset($params['offset'])
                ->get();

            $res['pages'] = ceil($resCount->count() / $params['limit']);

        }

        return $res;

    }


    public function getById($id)
    {
        return $this->model->with(['searchPreference', 'userImage'])->find($id);
    }


    /*============================*/

    public function getReceiverProfileById($params)
    {
        $res = $this->model->select('users.*')
            ->selectRaw('(SELECT IF (COUNT(id) = 2, 1, 0) AS is_matched FROM profile_like_dislike_boost pldb WHERE 
        	    (pldb.type = "like" OR pldb.type = "boost") AND ((pldb.reciever_id = ' . $params['user_id'] . ' AND pldb.sender_id = ' . $params['reciever_id'] . ') OR (pldb.reciever_id = ' . $params['reciever_id'] . ' AND pldb.sender_id = ' . $params['user_id'] . '))) as is_match')
            /*->join('profile_like_dislike_boost', 'profile_like_dislike_boost.reciever_id', '=', 'users.id')
            ->where('profile_like_dislike_boost.sender_id', '=', $params['user_id'])
            ->where(function ($query) {
                $query->where('profile_like_dislike_boost.type', '=', 'like')
                    ->orWhere('profile_like_dislike_boost.type', '=', 'boost');
            })*/
            ->where('id', $params['reciever_id'])
            ->with(['userImage', 'searchPreference'])->first();
        return $res;
    }

    /*============================*/

}
