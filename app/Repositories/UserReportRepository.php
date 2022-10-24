<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\UserReport;

class UserReportRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return UserReport::class;
    }

    public function getDataTable(){
        return $this->model
            ->select('r.id as reciever_id','r.full_name as reciever_name','s.id as sender_id'
                ,'s.full_name as sender_name','type','message','user_reports.created_at','user_reports.id')
            ->join('users as s','sender_id','=','s.id')
            ->join('users as r','reciever_id','=','r.id')
            ->where('r.status', '=', '1')
            ->get();
    }
}
