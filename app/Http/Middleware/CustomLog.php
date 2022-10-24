<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;
use Log;

class CustomLog {

	/**
	 * Prepare all logs in general
	 * 
	 * @param Illuminate\Http\Request $request
	 * @param Closure $next
	 */
	public function handle($request, Closure $next)
	{

		//PreMiddleware: Log request Headers & Parameters
		Log::info($request->url());
		Log::info($request->headers);
		Log::info($request);


		\DB::enableQueryLog();
		
		//Executed request & response
		$response = $next($request);
		
		//PostMiddleware: Log the database queries
		$queries = \DB::getQueryLog();
		Log::info($queries);
		
		return $response;
	}
}
