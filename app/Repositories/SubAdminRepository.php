<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\SubAdmin;

class SubAdminRepository extends BaseRepository {

    public function model() {
        return SubAdmin::class;
    }

    public function getDataTable() {
        $vendors = $this->model->where('role_id','=',3)->get();
        return $vendors;
    }

    public function getAllData() {
        return $this->model->all();
    }

    public function getData($id) {
        return $this->model->find($id);
    }

    public function setData($data) {
        return $this->model->create($data);
    }

    public function updateData($data, $id) {
        return $this->model->find($id)->update($data);
    }

    public function getVendors() {
        $vendors  = $this->model->select(['id','full_name'])->where('role_id','=',3)->get();
        return $vendors;
    }

}
