<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RESTAPIHelper;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use App\Transformers\Json;
use Illuminate\Http\Request;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function getResetToken(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        if ($request->wantsJson()) {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                #return response()->json(Json::response(null, trans('passwords.user')), 400);
                return RESTAPIHelper::emptyResponse($status = false, $dev_message = "User Not Found");
            }
           $token = '1234';
            return RESTAPIHelper::emptyResponse($status = false, $dev_message = "Code ");
        }
    }
}
