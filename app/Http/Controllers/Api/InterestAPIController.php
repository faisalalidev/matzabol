<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\InterestCreateRequest;
use App\Repositories\InterestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InterestAPIController extends Controller
{
    protected $interestRepository;
    public function __construct(InterestRepository $interestRepository)
    {
        $this->interestRepository = $interestRepository;
    }
    public function index()
    {
        return $this->interestRepository->getDataTable();

    }
    public function store()
    {

    }

    public function show()
    {

    }

    public function edit()
    {

    }
    public function update()
    {

    }

    public function destroy()
    {

    }

}
