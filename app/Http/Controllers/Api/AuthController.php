<?php

namespace App\Http\Controllers\Api;


use App\Helpers\RESTAPIHelper;
use App\Http\Requests\Api\CreateProfileRequest;
use App\Http\Requests\Api\NumberVerificationCodeRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\LogoutRequest;
use App\Http\Requests\Api\RegistationRequest;
use App\Http\Requests\Api\UpdateForgotPasswordRequest;
use App\Http\Requests\Api\VerifyCodeRequest;
use App\Models\UserImage;
use App\Repositories\SearchPreferenceRepository;
use App\Repositories\UdeviceRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use DB, Hash, Mail;
use App\Models\NumberVerification;
use Twilio;
use Twilio\Rest\Client;

class AuthController extends ApiBaseController
{


    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected $user, $uDevice, $searchPreference;

    public function __construct(UserRepository $userService, UdeviceRepository $udevice, SearchPreferenceRepository $sPreference)
    {
        parent::__construct($userService);
        $this->user = $userService;
        $this->uDevice = $udevice;
        $this->searchPreference = $sPreference;
    }

    public function login(LoginRequest $request)
    {
        if($request->social){
            return 'login with fb';
        }
      return $this->sendSms($request->phone_number);

    }

    public function sendSms($phone_number)
    {
        $params = [];
        $params['phone_number'] = $phone_number;
        $params['verification_code'] = rand(1111, 9999);
        $params['verification_code'] ='9999';
        $receiverNumber = $params['phone_number'];
        $message = "Use verification code ".$params['verification_code']." for Matzabol login" ;
        try {
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
            $client = new Client($account_sid, $auth_token);
//            $client->messages->create($receiverNumber, [
//                'from' => $twilio_number,
//                'body' => $message]);
            $numberVerification = NumberVerification::firstOrNew(['phone_number' => $phone_number]);
            $numberVerification->phone_number = $params['phone_number'];
            $numberVerification->verification_code = $params['verification_code'];
            if ($numberVerification->save()) {
                return RESTAPIHelper::response([], 200, 'Verification code send successfully.', $this->isBlocked);
            }
        } catch (Exception $e) {
            dd("Error: ". $e->getMessage());
        }
}
    public function verifyCode(VerifyCodeRequest $request)
    {
        #dd($request->phone_number);
        $params = [];

        $params['phone_number'] = $request->phone_number;
        $params['verification_code'] = $request->verification_code;

        try {
//            DB::table('password_resets')->insert(['email' => $email, 'code' => $code]);
//            \Illuminate\Support\Facades\Mail::send('email.verify', ['name' => $user->name, 'verification_code' => $code],
//                function ($mail) use ($email, $name, $subject) {
//                    $mail->from(getenv('FROM_EMAIL_ADDRESS'), "From Matzabol");
//                    $mail->to($email, $name);
//                    $mail->subject($subject);
//
//                });
            $verificationNumner = NumberVerification::where([
                'phone_number'      => $params['phone_number'],
                'verification_code' => $params['verification_code']
            ])->first();

            if ($verificationNumner) {
                if ($this->uDevice->getByDeviceToken($request->device_token)) {
                    $this->uDevice->deleteByDeviceToken($request->device_token);
                }
                $verificationNumner->delete();
                $res = $this->user->getByNumber($params['phone_number']);

                if ($res) {
                    $postData['device_type'] = $request->device_type;
                    $postData['device_token'] = $request->device_token;
                    $postData['user_id'] = $res->id;
                    $res->userDevice()->create($postData);

                    $token = JWTAuth::fromUser($res);
                    $twilioAccountSid = getenv("TWILIO_SID");
//                  $twilioApiKey= 'SK9d27b32ab75b2cc4dbe9fa5e8daf47a2';
//                  $twilioApiSecret = 'CIzMRiw0ydH4FzlkCFOq4zXoRJ5vcX2d';
                    $twilioApiKey= 'SKc877965934a3484debe1a49e3b73b38c';
                    $twilioApiSecret = 'I9EmGYyvROEi7R7f1Ra6bzhyUkSxofZy';
                    $identity = $res->id;
//                  https://console.twilio.com/us1/develop/conversations/manage/services?frameUrl=%2Fconsole%2Fconversations%2Fservices%3Fx-target-region%3Dus1
                    $serviceSid = 'ISc5bc180c61de4ba48a4eb3b418c25de5';
                    $Twiliotoken = new Twilio\Jwt\AccessToken(
                        $twilioAccountSid,
                        $twilioApiKey,
                        $twilioApiSecret,
                        86400,
                        $identity
                    );
                    //TWILIO Chat TOKEN
                    $chatGrant = new Twilio\Jwt\Grants\ChatGrant();
                    $chatGrant->setServiceSid($serviceSid);
                    $Twiliotoken->addGrant($chatGrant);
                    //TWILIO Video TOKEN
                    $videoGrant = new Twilio\Jwt\Grants\VideoGrant();
                    $roomName =  $postData['user_id'];
                    $videoGrant->setRoom($roomName);
                    $Twiliotoken->addGrant($videoGrant);
                    //TWILIO Voice TOKEN
                    $voiceGrant = new Twilio\Jwt\Grants\VoiceGrant();
                    $voiceGrant->setOutgoingApplicationSid($serviceSid);
                    $voiceGrant->setIncomingAllow(true);
                    //Add grant to token
                    $Twiliotoken->addGrant($voiceGrant);
                    // render token to string
//                    echo $token->toJWT();
                    $res['token'] = $token;
                    $res['twilio_accessToken'] = $Twiliotoken->toJWT();
                    return RESTAPIHelper::response(['user' => $res], 200, 'Code verified successfully.', $this->isBlocked, $token);
                } else {
                    return RESTAPIHelper::response([], 404, 'User Not Found.', $this->isBlocked);
                }
            }
            return RESTAPIHelper::response([], 404, 'Code not found.', $this->isBlocked);

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    public function resendCode(LoginRequest $request)
    {
        $params = [];
        $params['phone_number'] = $request->phone_number;

        try {
            $numberVerification = NumberVerification::where(['phone_number' => $request->phone_number])->first();
            if ($numberVerification) {

               return $this->sendSms($request->phone_number);
//                $message = 'Veil verification code ' . $numberVerification->verification_code;
//                if (Twilio::message($request->phone_number, $message)) {
//                    return RESTAPIHelper::response([], 200, 'Verification code send successfully.', $this->isBlocked);
//                }
            }

            return RESTAPIHelper::response([], 404, 'No record found against provided number.', $this->isBlocked);
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    public function createProfile(CreateProfileRequest $request)
    {
        $params = $request->all();
        try {
            $res = $this->user->create($params);
            if ($res) {
                $paramsDevices = [
                    'device_type'  => $request->device_type,
                    'device_token' => $request->device_token,
                    'user_id'      => $res->id
                ];
                $this->searchPreference->create(['user_id' => $res->id]);
                $this->uDevice->create($paramsDevices);
                $resImages = [];
                if ($request->hasFile('image')) {
                    foreach ($request->image as $key => $photo) {
                        $filename = $photo->store('users');
                        $resImages[] = UserImage::create([
                            'user_id'    => $res->id,
                            'image'      => $filename,
                            'sort_order' => $key
                        ]);
                    }
                }

                $res['user_image'] = $resImages;

                $token = JWTAuth::fromUser($res);
                return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($res->id)], 200, 'Profile created successfully.', $this->isBlocked, $token);
            }

            return RESTAPIHelper::response([], 404, 'Error in create profile.', $this->isBlocked);


        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    public function logout(LogoutRequest $request)
    {
        try {
            JWTAuth::invalidate($request->bearerToken());
            $this->uDevice->deleteByDeviceToken($request->device_token);
            return RESTAPIHelper::response([], 200, 'Logout successfully.', $this->isBlocked);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    /************** Tested ***********/


    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token', $verification_code)->first();

        if (!is_null($check)) {
            $user = $this->user->find($check->user_id);
            if ($user->is_verified == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account already verified..'
                ]);
            }
            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully verified your email address.'
            ]);
        }

        return response()->json(['success' => false, 'error' => "Verification code is invalid."]);
    }

    public function getCodeByNumber(NumberVerificationCodeRequest $request)
    {

        $user = $this->user->getByEmail($request->email);
        if (!$user) {


            $error_message = "Your email address was not found.";
            return RESTAPIHelper::response([], 404, $error_message);
        }


        $code = rand(1111, 9999);

        $subject = "Forgot Password Verfication Code";
        try {
            $email = $user->email;
            $name = $user->name;

            $check = DB::table('password_resets')->where('email', $email)->first();
            if ($check) {
                DB::table('password_resets')->where('email', $email)->delete();
            }

            DB::table('password_resets')->insert(['email' => $email, 'code' => $code]);
            \Illuminate\Support\Facades\Mail::send('email.verify', ['name' => $user->name, 'verification_code' => $code],
                function ($mail) use ($email, $name, $subject) {
                    $mail->from(getenv('FROM_EMAIL_ADDRESS'), "From Education-USA");
                    $mail->to($email, $name);
                    $mail->subject($subject);

                });

        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return RESTAPIHelper::response([], 404, $error_message);
        }

        return RESTAPIHelper::response([], 200, 'Verification Code Send To Your Email');


    }

    public function register(Request $request)
    {
        $postData = $request->all();
        if($request->loginKey == 'phone'){
            $validated = $request->validate([
                'phone_number' => 'required',
            ]);
            $number = $this->user->getByNumber($request->phone_number);
           if($number){
                return RESTAPIHelper::response([], 401, 'Phone Number Already Exists');
            }
            $postData['email'] = $request->phone_number.'@matzabol.com';
            $postData['name'] = ($request->name) ? $request->name : '';
            $postData['fname'] = ($request->fname) ? $request->fname : '';
            $postData['lname'] = ($request->lname) ? $request->lname : '';
            $postData['password'] = bcrypt(rand(11111111,99999999));
            $postData['device_token'] = $request->device_token;
            $postData['device_type'] = $request->device_type;
            $postData['role_id'] = 2;
            if ($this->uDevice->getByDeviceToken($request->device_token)) {
                $this->uDevice->deleteByDeviceToken($request->device_token);
            }
            $user = $this->user->create($postData);
            $credentials = [
                'email'       => $postData['email'],
                'password'    => $postData['password'],
                'is_verified' => 1
            ];
            $token = JWTAuth::attempt($credentials);
            $userById = $this->user->find($user->id);
            $userById['token'] = $token;
            return $this->sendSms($request->phone_number);
        }
        else if($request->loginKey == 'email'){
            $validated = $request->validate([
                'email' => 'required',
            ]);
            $email = $this->user->getByEmail($request->email);
            if($email){
                return RESTAPIHelper::response([], 401, 'Email Already Exists');
            }
            $postData['email'] = $request->email;
            $postData['name'] = ($request->name) ? $request->name : '';
            $postData['fname'] = ($request->fname) ? $request->fname : '';
            $postData['lname'] = ($request->lname) ? $request->lname : '';
            $postData['password'] = bcrypt(rand(11111111,99999999));
            $postData['device_token'] = $request->device_token;
            $postData['device_type'] = $request->device_type;
            $postData['role_id'] = 2;
            if ($this->uDevice->getByDeviceToken($request->device_token)) {
                $this->uDevice->deleteByDeviceToken($request->device_token);
            }

            $user = $this->user->create($postData);

            $credentials = [
                'email'       => $postData['email'],
                'password'    => $postData['password'],
                'is_verified' => 1
            ];
            $token = JWTAuth::attempt($credentials);
            $userById = $this->user->find($user->id);
            $userById['token'] = $token;

//            return $this->sendSms($request->phone_number);
        }
        return RESTAPIHelper::response(['user' => $userById]);

    }

    public function updateForgotPassword(UpdateForgotPasswordRequest $request)
    {
        $postData['password'] = bcrypt($request->password);

        try {
            $data = $this->user->getByEmail($request->email);
            $this->user->update($postData, $data->id);

            if ($this->user->getUserStatus($data->id) == 0) {
                $this->isBlocked = 1;
            }

            return RESTAPIHelper::response([], 200, 'Success', $this->isBlocked);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return RESTAPIHelper::response([], 404, $error_message);
        }


    }


}