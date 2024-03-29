<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Event;

class EventRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Event::class;
    }

    public function getEvent($id)
    {
        return $this->model->where('id',$id)
            ->first();
    }
    public function getDataTable($data = [])
    {
        return $this->model
            ->orderBy('created_at', 'ASC')
            ->get();
    }
    public function setData($data) {
        return $this->model->create($data);
    }
}
