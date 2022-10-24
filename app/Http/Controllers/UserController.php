<?php namespace App\Http\Controllers;

use App\Services\User\UserService as User;
use Datatables;
use Illuminate\Http\Request;
use Stripe;

class UserController extends Controller
{
	protected $userService;
	
	public function __construct(User $userService){
		$this->userService = $userService;
	}

	public function login(){
		return $this->userService->login();
	}
	
	public function logout(){
		return $this->userService->logout();
	}

    public function usersHome(Request $request)
    {
        $users = $this->userService->getUsers();
        if ($request->ajax()) {
            return view('site.users.load', ['users' => $users])->render();
        }
        return view('site.users.home', ['users' => $users]);
    }

	public function usersHomeInfinite()
	{
		$users = $this->userService->getUsers();
		return view('site.users.home-infinite', ['users' => $users]);
	}

}
