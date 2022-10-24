<?php

namespace App\Libraries;
use App\Helpers\RESTAPIHelper;
use Response;
use Log;
use App\Helper;
trait APIResponse
{

	public function sendResponse($status_code = 200, $response = null, $error = [], $custom_error_code = null)
    {
    	$status = ($status_code === 200) ? true : false;
		$response= !empty($response)||is_array($response) ? $response: null;
    	$error = !empty($error) ? [
    			'custom_code' => $custom_error_code,
    			'message' => $error
    	]: null;
    	
    	$return = [
    		'status' 	=> $status,
    		'response' 	=> $response,
    		'error' 	=> $error
    	];
    	
    	Log::info(print_r($return,true));
    	return Response::json($return, $status_code);

    }

}