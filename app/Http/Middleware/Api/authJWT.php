<?php

namespace App\Http\Middleware\Api;

use Tymon\JWTAuth\Middleware\GetUserFromToken;
use App\Helpers\RESTAPIHelper;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class authJWT extends GetUserFromToken{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return RESTAPIHelper::response([], 404, 'Token not provided');
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return RESTAPIHelper::response([], 404, 'Token expired.');
        } catch (JWTException $e) {
            return RESTAPIHelper::response([], 404, 'Invalid token.');
        }

        if (! $user) {
            return RESTAPIHelper::response([], 404, 'User not found');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }

}

