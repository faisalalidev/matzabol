<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Http\Requests\Api\CreateContactusRequest;
use App\Repositories\ContactusRepository;
use App\Repositories\UserRepository;


class ContactusController extends ApiBaseController
{
    protected $user, $contactus;

    public function __construct(UserRepository $user, ContactusRepository $service)
    {
        parent::__construct($user);
        $this->contactus = $service;
    }

    public function store(CreateContactusRequest $request)
    {
        $params = $request->all();

        try {
            if ($this->contactus->create($params)) {
                $this->getUserBlockedStatus($request->user_id);
                return RESTAPIHelper::response([], 200, 'Success', $this->isBlocked);
            }

            return RESTAPIHelper::response([], 404, 'Error', $this->isBlocked);


        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }
}
