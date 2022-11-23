<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
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

        return RESTAPIHelper::response(['interest' => $this->interestRepository->getDataTable()], 200, 'Interest Fetch successfully.', $this->isBlocked);
//        return $this->interestRepository->getDataTable();

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

    public function genderList()
    {
        $gender[1] = 'Women';
        $gender[2] = 'Man';
        $gender[3] = 'Other';
        return RESTAPIHelper::response( $gender, 200, 'Gender Fetch successfully.', $this->isBlocked);
    }
}
