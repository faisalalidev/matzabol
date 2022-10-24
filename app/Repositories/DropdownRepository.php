<?php

namespace App\Repositories;

use Czim\Repository\BaseRepository;
use App\Models\Dropdown;

class DropdownRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Dropdown::class;
    }



}
