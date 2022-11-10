<?php

namespace App\Repositories;

use App\Model\UserPrompt;
use Czim\Repository\BaseRepository;

class UserPromptRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return UserPrompt::class;
    }
    public function getDataTable($user_id = 0)
    {

        return $this->model->when($user_id, function ($query) use ($user_id){
            $query->where('user_id', $user_id);
        })->orderBy('created_at', 'ASC')
            ->get();
    }
    public function setData($data) {
        return $this->model->create($data);
    }
}
