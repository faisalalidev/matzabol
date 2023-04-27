<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\ProfileLikeDislike;

class ProfileLikeDislikeRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return ProfileLikeDislike::class;
    }

    public function isActivityExists($data)
    {
        return $this->model
            ->where([
                ['sender_id', '=', $data['sender_id']],
                ['reciever_id', '=', $data['reciever_id']],
            ])
            ->where(function ($q) {
                $q->where('is_like', '=', '1')->orWhereIn('type', ['like', 'dislike']);
            })
            //->whereIn('type', ['like', 'dislike'])
            ->first();
    }

    public function isLikeActivityExists($data)
    {
         return $this->model
            ->where([
                ['sender_id', '=', $data['sender_id']],
                ['reciever_id', '=', $data['reciever_id']],
            ])
            ->where(function ($q) {
                $q->where('is_like', '=', '1')->orWhere('type', '=', 'like');
            })
            ->first();
    }

    public function isDislikeActivityExists($data)
    {
        return $this->model
            ->where([
                ['sender_id', '=', $data['sender_id']],
                ['reciever_id', '=', $data['reciever_id']],
            ])
            ->where('is_like', '=', '0')
            ->where('is_boost', '=', '0')
            ->where('type', '=', 'dislike')
            ->first();
    }

    public function isMatch($data)
    {
        $match = $this->model->select('id')
            ->selectRaw('(SELECT IF (COUNT(id) = 2, 1, 0) AS is_matched FROM profile_like_dislike_boost pldb WHERE 
        	    (pldb.type = "like" OR pldb.type = "boost") AND ((pldb.reciever_id = ' . $data['reciever_id'] . ' AND pldb.sender_id = ' . $data['sender_id'] . ') OR (pldb.reciever_id = ' . $data['sender_id'] . ' AND pldb.sender_id = ' . $data['reciever_id'] . ')))
        	    as is_match')
            ->first();
        if ($match) {
            if($match->is_match){
                return true;
            }
            else {
                return false;
            }
        } else {
            return false;
        }

        /*return $this->model->where([
            ['sender_id', '=', $data['reciever_id']],
            ['reciever_id', '=', $data['sender_id']],
        ])
            //->where('type','=', 'like')
            ->where(function ($q) {
                $q->where('is_like', '=', '1')->orWhere('is_boost', '=', '1');
            })
            ->first();*/
    }


    public function updateOrCreate(array $condition, array $array)
    {
        return $this->model->updateOrCreate($condition, $array);
    }


    public function isUserUnMatch($data)
    {

        return $this->model
            ->where([
                ['sender_id', '=', $data['sender_id']],
                ['reciever_id', '=', $data['reciever_id']],
            ])
            ->first();
    }

    public function deleteUnMatchUser($data)
    {
        //dd($data);
        return $this->model
            ->where([
                ['sender_id', '=', $data['sender_id']],
                ['reciever_id', '=', $data['reciever_id']],
            ])
            ->delete();
    }


}
