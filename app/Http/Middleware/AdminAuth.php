<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Flash;
use Illuminate\Support\Facades\Hash;


class AdminAuth {


    public function __construct() {


    }

    /**
     * Validate Token request
     * 
     * @param unknown $request
     * @param Closure $next
     * @return Ambigous <\Symfony\Component\HttpFoundation\Response, \Illuminate\Contracts\Routing\ResponseFactory>
     */
    public function handle($request, Closure $next, $guard = null) {
        $request['admin_data'] = Auth::user();
        if (Auth::guard($guard)->guest()) {
            //dd(Auth::user());
            if ($request->ajax() || $request->wantsJson()) {

                return response('Unauthorized.', 401);
            }
            else {
                Flash::error('Please Login First');
                return redirect()->guest('admin/login');
            }
        }
        if (session('full_authenticated')) {
            if(Auth::user()->status == '1') {
                return $next($request);
            }
            Auth::logout();
            Session::flush();
            Flash::error('Your account has been blocked by admin !');
            return redirect()->guest('admin/login');
        } else {
            Auth::logout();
            Session::flush();
            return redirect()->guest('admin/login');
        }
    }

}
