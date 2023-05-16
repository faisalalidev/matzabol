<?php


Route::get('/resize/{img_dir}/{img}/{h?}/{w?}', function ($img_dir, $img, $h = '', $w = '') {
    try {
        if ($h && $w) {
            return \Image::make(asset("storage/app/$img_dir/$img"))->resize($h, $w)->response('png');
        } else {
            return response(file_get_contents(asset("storage/app/$img_dir/$img")))
                ->header('Content-Type', 'image/png');
        }
    } catch (\Exception $e) {
        return \App\Helpers\RESTAPIHelper::response([], 500, $e->getMessage());
    }
});

/*
 * Users API routes
 * */
Route::get('/test', function () {
    return ini_get('memory_limit');
});

Route::post('login', 'Api\AuthController@login');
Route::post('verify-code', 'Api\AuthController@verifyCode');
Route::post('resend-code', 'Api\AuthController@resendCode');
Route::post('create-profile', 'Api\AuthController@createProfile');


/*CMS Pages API routes*/
Route::get('cms-page', 'Api\CmsPageController@getByType');

Route::get('email-password-code', 'Api\AuthController@getForgotPasswordCode');
Route::post('register', 'Api\AuthController@register');

Route::post('recover', 'Api\AuthController@recover');
Route::post('guest-login', 'Api\AuthController@guestLogin');

Route::post('/password/email', 'Auth\ForgotPasswordController@getResetToken');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');


/*Public Dropdowns*/
Route::get('get-dropdowns', 'Api\DropdownController@getDropdowns');


Route::group(['middleware' => ['jwt.customAuth']], function () {
    Route::post('update-profile/{id}', 'Api\UserController@updateProfile');
    Route::post('events/join', 'Api\EventAPIController@join');
    Route::get('events-user/{id}', 'Api\EventAPIController@eventUser');
    Route::post('events-user-joined', 'Api\EventAPIController@eventUserJoined');
    Route::post('update-user-image', 'Api\UserController@updateImage');
    Route::resource('events', 'Api\EventAPIController');
});

/*****************************JWT middleware Auths****************************************************/
/*****************************JWT middleware Auths****************************************************/
/*****************************JWT middleware Auths****************************************************/

/*User Routes*/
Route::get('get-profile', 'Api\UserController@getUserProfile');
//Route::post('update-profile', 'Api\UserController@updateProfile');
Route::post('update-aboutme', 'Api\UserController@updateProfile');
Route::post('update-notification-settings', 'Api\UserController@updateProfile');

Route::get('get-user-images', 'Api\UserController@getUserImages');
Route::post('search-profiles', 'Api\UserController@getSearchProfiles');
Route::post('user/update-password', 'Api\UserController@updatePassword');
Route::post('logout', 'Api\AuthController@logout');
Route::post('user-activity', 'Api\UserController@userProfileActivity');
Route::post('update-search-preferences', 'Api\UserController@updateUserSearchPreference');
Route::get('get-my-liked-profiles', 'Api\UserController@getRelativeProfilesByUserId'); //Get those users profiles, I have liked or boosted profiles
Route::get('get-my-boosters', 'Api\UserController@getUserBoosters');  //get those users profile who boost me
Route::post('who-has-liked-me', 'Api\UserController@getWhoLikedMe'); // get those users profiles wo liked me
Route::put('report-user', 'Api\UserController@userReport'); //report user fake,bad language, others etc
Route::post('rewind-user', 'Api\UserController@rewindUser'); // rewind user
Route::get('get-notification-count', 'Api\UserController@getNotificationCount'); // Get notification count
Route::get('read-all-notification', 'Api\UserController@markNotificationRead'); // Mark All Notification Read
Route::get('get-user-thread-id', 'Api\UserController@getChatThreadIdAndUser');
Route::post('un-match-user', 'Api\UserController@unMatchUserByThreadID');
Route::post('un-match', 'Api\UserController@UnMatched');
Route::post('conversation', 'Api\UserController@addConversation');
Route::post('create-video-call', 'Api\UserController@createVideoCall');
Route::post('/incoming-call', function (\Illuminate\Http\Request $request) {
    $data = [
        'from' => $request->from,
        'to' => $request->to,
        'request' => json_encode($request->all())
    ];
    \Illuminate\Support\Facades\DB::table('test_call')->insert($data);
    // Perform any necessary logic, such as validating the caller or initiating the call
//  Working Code
//    $response = new \Twilio\TwiML\VoiceResponse();
//    $dial = $response->dial();
//    $dial->client('1096');
//    return $response;
//    EndCODE
    $response = new \Twilio\TwiML\VoiceResponse();
    $dial = $response->dial();
    $dial->client('1096');
    return $response;
});

Route::post('/status-callback', function (\App\Http\Requests\Request $request) {
    $callSid = $request->input('CallSid');
    $callStatus = $request->input('CallStatus');

    // Perform any necessary actions based on the call status

    return response()->json(['status' => 'success']);
});

Route::get('conversation/{id}', 'Api\UserController@getConversation');
/*ContactUs Routes*/
Route::post('contact-us', 'Api\ContactusController@store');

/*Faq*/
Route::get('get-faqs', 'Api\FaqController@getAllFaq');
Route::get('interests/gender-list', 'Api\InterestAPIController@genderList');


/*interests*/
Route::resource('interests', 'Api\InterestAPIController');
Route::resource('religion', 'Api\ReligionAPIController');
Route::resource('prompts', 'Api\PromptAPIController');
Route::resource('user_prompts', 'Api\UserPromptAPIController');




/*Notification*/
Route::get('get-admin-notifications', 'Api\NotificationController@getAdminNotificationByUserID'); // get admin notifications messages
Route::post('mark-read-notifications', 'Api\NotificationController@markNotificationReadByUserID'); // mark read admin notifications messages

/*****************************JWT middleware Auths****************************************************/
/*****************************JWT middleware Auths****************************************************/
/*****************************JWT middleware Auths****************************************************/
/*****************************JWT middleware Auths****************************************************/
/*****************************Ending middleware****************************************************/


/*Testing API*/
Route::post('delete-profile', 'Api\UserController@deleteProfile');
Route::get('get-profile-without-auth', 'Api\UserController@getUserProfileWithoutAuth');

//Route::get('test', function () {
//    $userid = 193;
//    $res = DB::select('SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=?) OR (ur.sender_id=c.sender_id AND reciever_id=?) WHERE cr.type <> \'read_by_user\' AND c.sender_id <> ? AND tu.user_id = ? AND c.deleted_at IS NULL AND (c.deleted_by <> ? OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))', [$userid, $userid, $userid, $userid, $userid]);
//    dd($res);
//
//});