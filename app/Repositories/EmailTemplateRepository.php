<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\EmailTemplate;

class EmailTemplateRepository extends BaseRepository {

    public function model() {
        return EmailTemplate::class;
    }

    public function getDataTable() {
        $templates = $this->model->get();
        return $templates;
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
}
