<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\SearchPreference;

class SearchPreferenceRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return SearchPreference::class;
    }

    public function updateByUserId(array $data,$id)
    {
        return $this->model->where('user_id',$id)->update($data);
    }
}
