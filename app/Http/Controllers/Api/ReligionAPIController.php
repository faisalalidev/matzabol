<?php

namespace App\Http\Controllers\Api;

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
        return $this->religionRepository->getDataTable();
    }
}
