<?php

namespace App\Http\Middleware;

use App\Exceptions\UnAuthorizedRequestException;
use Closure;
use App\Libraries\APIResponse;
use Config;
use DB;

class ValidateClient
{
    use APIResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$client_id = $request->header('client-id');
    	$authorization_header = $request->header('Authorization');
    	$client_secret = str_replace("Basic ","",$authorization_header);
        $client = DB::table('client')
                    ->where('client_id', $client_id)
                    ->where('client_secret', $client_secret)
                    ->first();
        if($client)
            return $next($request);
        

       // throw new UnAuthorizedRequestException;
         return $this->sendResponse(Config::get('error.code.NOT_FOUND'),
                null,
                ['Unauthorized Request'],
                Config::get('error.code.NOT_FOUND'));
    }
}
