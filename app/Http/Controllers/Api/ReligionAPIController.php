<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Repositories\ReligionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReligionAPIController extends Controller
{
    protected $religionRepository;
    public function __construct(ReligionRepository $religionRepo)
    {
        $this->religionRepository = $religionRepo;
    }

    public function index()
    {
        return RESTAPIHelper::response(['interest' => $this->religionRepository->getDataTable()], 200, 'Religion Fetch successfully.', $this->isBlocked);
//        return $this->religionRepository->getDataTable();
    }
}
