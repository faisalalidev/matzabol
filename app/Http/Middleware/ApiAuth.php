<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;
use App\Exceptions\UnAuthorizedRequestException;
use App\Libraries\APIResponse;
use Config;
use DB;

class ApiAuth {

	use APIResponse;
	/**
	 * Validate Token request
	 * 
	 * @param unknown $request
	 * @param Closure $next
	 * @return Ambigous <\Symfony\Component\HttpFoundation\Response, \Illuminate\Contracts\Routing\ResponseFactory>
	 */

	public function handle($request, Closure $next)
	{
		$access_token = $request->header('Authorization');

		$access_token = str_replace("Bearer ", "", $access_token);
		
		if($access_token) {
			$user = \App\Models\User::where('access_token', $access_token)->first();

			if($user) {
				$request->merge(array("user" => $user));

				return $next($request);
			}
		}
		return $this->sendResponse(401,
									null,
									['Unauthorized access_token'],
									401)->header('Content-Type', 'application/json');

	}
}
