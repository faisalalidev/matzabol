<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Contactus;

class ContactusRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Contactus::class;
    }

    public function getDataTable()
    {
        return $this->model
            ->select('u.full_name as user_name', 'contactus.*')
            ->join('users as u', 'user_id', '=', 'u.id')
            //->where('r.status', '=', '1')
            ->orderBy('contactus.id', 'DESC')
            ->get();
    }

}
