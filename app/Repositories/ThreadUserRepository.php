<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\ThreadUser;

class ThreadUserRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return ThreadUser::class;
    }

    public function getThreadIdByUsersID($params){

        return $this->model
            ->select('id','thread_id')
            ->selectRaw('COUNT(thread_id) as COUNT')
            ->whereIn('user_id',[$params['user_id'],$params['reciever_id']])
            ->groupBy('thread_id')
            ->having('COUNT',2)
            ->orderBy('id','desc')
            ->first();
    }

    public function deleteByThreadId($data){
        return $this->model
            ->where([
                ['thread_id', '=', $data['thread_id']],
                ['user_id', '=', $data['sender_id']],
            ])
            ->delete();
    }}
