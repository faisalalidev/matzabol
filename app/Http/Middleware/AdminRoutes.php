<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Auth\Guard;

class AdminRoutes
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if (Auth::user()->role_id != 1) {
            return response('Unauthorized.', 401);
        } else {
            return $next($request);
        }
    }
}