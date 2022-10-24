<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\CmsPage;

class CmsPageRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return CmsPage::class;
    }
    public function getDataTable()
    {
        return $this->model->all();
    }

    public function getByType($type, $status = '1')
    {
        return $this->model->where(['type' => $type,'status' => $status])->first();
    }

}
