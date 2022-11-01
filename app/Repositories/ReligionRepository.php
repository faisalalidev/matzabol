<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Religion;

class ReligionRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Religion::class;
    }

    public function getDataTable()
    {
        return $this->model
            ->orderBy('created_at', 'ASC')
            ->get();
    }
}
