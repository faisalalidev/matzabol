<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use Flash;
use App\Services\User\UserService as User;

class SiteAuth
{
  /*  protected $favorite,$user;
    public function __construct(Favorite $favorite, User $user)
    {
        $this->favorite = $favorite;
        $this->user = $user;
    }*/

   /* public function handle($request, Closure $next, $guard = null)
    {

        $request['user_data'] = Auth::user();



        if (Auth::guard($guard)->guest()) {

            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            else {
                Flash::error('Please Login First');
                return redirect()->guest('signin');
            }
        };
        if (session('user_authenticated')) {
            $favoriteCounter = $this->favorite->getUserFavorites(Auth::id());
            $userLoginImage = $this->user->getData(Auth::id());
            Session::put('favorite_counter',$favoriteCounter);
            Session::put('login_avatar',$userLoginImage->login_avatar);
            return $next($request);
        } else {
            Auth::logout();
            Session::flush();
            return redirect()->guest('signin');
        }

    }*/
}
