<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;


class ApiBaseController extends Controller
{
    protected $isBlocked = 0;
    private $user;

    public function __construct(UserRepository $userService)
    {
        $this->user = $userService;
    }

    protected function getUserBlockedStatus($user_id)
    {

        if ($this->user->getUserStatus($user_id) == 0) {
            $this->isBlocked = 1;
        }
    }
}
