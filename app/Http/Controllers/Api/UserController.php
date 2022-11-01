<?php

namespace App\Http\Controllers\Api;

use App\Models\UserInterest;
use function App\Helper\sendPushNotifications;
use App\Helper\Utils;
use App\Helpers\RESTAPIHelper;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\GetActivityRequest;
use App\Http\Requests\Api\ProfileActivityRequest;
use App\Http\Requests\Api\SearchProfilesRequest;
use App\Http\Requests\Api\UpdateSearchPerferenceRequest;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use App\Http\Requests\Api\UnMatchActivityRequest;
use App\Http\Requests\Api\UserIDRequest;
use App\Http\Requests\Api\UserImageRequest;
use App\Http\Requests\Api\UserReportRequest;
use App\Http\Requests\Frontend\UserRegisterRequest;
use App\Models\UserImage;
use App\Repositories\ChatRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ProfileLikeDislikeRepository;
use App\Repositories\SearchPreferenceRepository;
use App\Repositories\ThreadRepository;
use App\Repositories\ThreadUserRepository;
use App\Repositories\UdeviceRepository;
use App\Repositories\UserReportRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Config;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use JWTAuth, Validator;
use DB;
use Mockery\Exception;


class UserController extends ApiBaseController
{

    protected $user, $userActivity, $searchPreferences, $uReport, $thread, $udevice, $notification, $threadUser, $chat;

    /*********************************Old Functions By Addy*********************************/
    public function __construct(UserRepository $userService,
                                ProfileLikeDislikeRepository $userActivity,
                                SearchPreferenceRepository $sPerference,
                                UserReportRepository $uReport,
                                ThreadRepository $thread,
                                UdeviceRepository $udevice,
                                NotificationRepository $notify,
                                ThreadUserRepository $threadUser,
                                ChatRepository $chats

    )
    {
        parent::__construct($userService);
        $this->user = $userService;
        $this->userActivity = $userActivity;
        $this->searchPreferences = $sPerference;
        $this->uReport = $uReport;
        $this->udevice = $udevice;
        $this->thread = $thread;
        $this->notification = $notify;
        $this->threadUser = $threadUser;
        $this->chat = $chats;
    }

    public function getUserProfile(UserIDRequest $request)
    {
        try {
            $res = $this->user->getByIdWithImages($request->user_id);
            if ($res) {
                $this->getUserBlockedStatus($res->id);
                return RESTAPIHelper::response(['user' => $res], 200, 'Profile found successfully.', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'No record found.', $this->isBlocked);
            }

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    public function getUserImages(UserIDRequest $request)
    {

        try {
            $res = UserImage::where([
                'user_id' => $request->user_id,
                'status'  => '1'
            ])->get();

            if ($res) {
                $this->getUserBlockedStatus($request->user_id);
                return RESTAPIHelper::response(['user_images' => $res], 200, 'User images found successfully.', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'No record found.', $this->isBlocked);
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $params = $request->all();

//        unset($params['user_id']);
        try {
            $res = $this->user->update($params, $request->id);
            if ($request->device_token && $request->device_type) {
                $deviceData['device_token'] = $request->device_token;
                $deviceData['device_type'] = $request->device_type;
                $deviceData['user_id'] = $request->user_id;
                $this->udevice->updateDeviceToken($deviceData);
            }
            if ($request->has('interest')) {
                try {
                    $userInterest =  UserInterest::where('user_id',$request->id)->get();
                    $interest = explode(',', $request->interest);
                        foreach ($interest as $row) {
                            $data['user_id'] = $request->id;
                            $data['interest_id'] = $row;
                            UserInterest::updateOrCreate($data);
                        }
                } catch (Exception $exception) {
                    $data['user_id'] = $request->id;
                    $data['interest_id'] = $request->interest;
                    UserInterest::updateOrCreate($data);
//                    UserInterest::
                }
            }
            if ($res) {
                $this->getUserBlockedStatus($request->user_id);
                return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($request->user_id)], 200, 'Profile Updated successfully.', $this->isBlocked);
            }

            return RESTAPIHelper::response([], 404, 'Error in update profile.', $this->isBlocked);

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    public function updateImage(UserImageRequest $request)
    {
        try {
            switch ($request->type) {
                case 'insert': {
                    if ($request->hasFile('image')) {
                        $filename = $request->image->store('users');
                        $res = UserImage::create([
                            'user_id'    => $request->user_id,
                            'image'      => $filename,
                            'sort_order' => $request->sort_order
                        ]);

                        if ($res) {
                            return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($request->user_id)], 200, 'Insert successfully.', $this->isBlocked);
                        }
                    }
                    break;
                }
                case 'update': {
                    if ($request->hasFile('image')) {
                        $filename = $request->image->store('users');
                        $res = UserImage::where('id', $request->image_id)
                            ->update([
                                'image' => $filename
                            ]);

                        if ($res) {
                            Storage::delete($request->old_image);
                            return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($request->user_id)], 200, 'Image Updated successfully.', $this->isBlocked);
                        }
                    }
                    break;
                }
                case 'update_order': {
                    /*
                     *  Switch images orders by id
                     * */
                    $order_from = explode('-', $request->order_from);
                    $order_to = explode('-', $request->order_to);

                    $order_from_res = UserImage::where('id', $order_from[0])
                        ->update([
                            'sort_order' => $order_from[1]
                        ]);

                    if ($order_from_res) {
                        $order_from_res = UserImage::where('id', $order_to[0])
                            ->update([
                                'sort_order' => $order_to[1]
                            ]);
                        return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($request->user_id)], 200, 'Order updated successfully.', $this->isBlocked);
                    }
                    break;
                }
                case 'delete': {

                    $res = UserImage::destroy($request->image_id);
                    if ($res) {
                        Storage::delete($request->old_image);
                        return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($request->user_id)], 200, 'Deleted successfully.', $this->isBlocked);
                    }
                    break;
                }
                case 'default': {
                    return RESTAPIHelper::response([], 404, 'Error', $this->isBlocked);
                    break;
                }
            }

            return RESTAPIHelper::response([], 404, 'Error', $this->isBlocked);

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    public function getSearchProfiles(SearchProfilesRequest $request)
    {
        $params = $request->all();
        $postData['is_rewind'] = '0';
        $postData['rewind_date'] = '';
        $params['limit'] = (isset($params['limit'])) ? $params['limit'] : Config::get('constants.limit');

        try {
            #  $userData = Auth::user();
            $userData = $this->user->getById($request->user_id);

            if ($userData) {
                /*Is Rewind code*/
                /* $is_rewind = $userData->is_rewind;
                 if ($userData->is_rewind == '1' && $userData->rewind_date) {
                     $t1 = strtotime(Carbon::now());
                     $t2 = strtotime($userData->rewind_date);
                     $diff = $t1 - $t2;
                     $hours = intval($diff / (60 * 60));
                     if ($hours > 24) {
                         $res = $this->user->update($postData, $request->user_id);
                         if ($res) $is_rewind = '0';
                     }
                 }*/


                $params['user'] = $userData;
                $params['user_preference'] = $this->searchPreferences->find($userData->id, ['*'], 'user_id');

                if ($request->pending_requests) // Deal with pending request
                {
                    $this->pendingRequests($request->pending_requests);
                }


                $res = $this->user->getSearchProfiles($params);
                if ($res) {
                    $this->getUserBlockedStatus($request->user_id);
                    return RESTAPIHelper::response(['user' => $res['data']], 200, 'Profile found successfully.', $this->isBlocked, '', $res['pages']);
                } else {
                    return RESTAPIHelper::response([], 404, 'No record found.', $this->isBlocked);
                }
            } else {
                return RESTAPIHelper::response([], 404, 'User not found.');
            }


        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        $user = $this->user->findOrFail($request->user_id);

        $updateData = $request->all();
        $hashedPassword = $user->password;

        if (Hash::check($updateData['old_password'], $hashedPassword)) {
            $newPassword = bcrypt($updateData['password']);
            $updateData['password'] = $newPassword;

            if ($this->user->update($updateData, $user->id)) {
                if ($this->user->getUserStatus($request->user_id) == 0) {
                    $this->isBlocked = 1;
                }
                return RESTAPIHelper::response([], 200, 'Success', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'Error');
            }
        } else { #$request->session()->flash('failure', 'Old Password Incorrect');
            return RESTAPIHelper::response([], 404, 'Old Password Incorrect');
        }
    }

    public function getUserProfileWithoutAuth(UserIDRequest $request)
    {
        $res = $this->user->getByIdWithImages($request->user_id);

        try {
            if ($res) {
                $this->getUserBlockedStatus($res->id);
                $token = JWTAuth::fromUser($res);
                return RESTAPIHelper::response(['user' => $res], 200, 'Profile found successfully.', $this->isBlocked, $token);
            } else {
                return RESTAPIHelper::response([], 404, 'No record found.', $this->isBlocked);
            }

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    public function deleteProfile(UserIDRequest $request)
    {
        $res = $this->user->delete($request->user_id);
        try {
            if ($res) {
                $this->chat->deleteMessagesByUser($request->user_id);
                return RESTAPIHelper::response([], 200, 'Deleted successfully.', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'No record found.', $this->isBlocked);
            }

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    /***************************New functions By Max***************************/
    /***************************New functions By Max***************************/
    /***************************New functions By Max***************************/
    /*like , dislike and boost api function*/
    public function userProfileActivity(ProfileActivityRequest $request)
    {
        $res['match'] = false;

        try {
            $postData = $request->all();
            $postData['sender_id'] = $postData['user_id'];


            Switch ($request->type) {
                case 'like':
                    if (!$this->userActivity->isLikeActivityExists($postData)) {
                        if (!$this->userActivity->isMatch($postData)) {
                            //$postData['is_like'] = "1";
                            //$userActivity = $this->userActivity->create($postData);
                            $userActivity = $this->userActivity->updateOrCreate([
                                'sender_id'   => $postData['sender_id'],
                                'reciever_id' => $postData['reciever_id']
                            ], $postData);

                            /*Create A Notification for receiver*/
                            $notificationData['message'] = Config::get('constants.notifications')['4']['msg'];
                            $notificationData['action_type'] = Config::get('constants.notifications')['4']['title'];
                            $notificationData['ref_id'] = $userActivity->id;
                            $notification = $this->notification->create($notificationData);
                            $notification->users()->attach($userActivity->reciever_id);

                            $isMatch = $this->userActivity->isMatch($postData);

                            if ($isMatch) {
                                $thread_id = $this->createThread($postData);
                                $extraPayLoadData['thread_id'] = $thread_id;
                                $extraPayLoadData['user_id'] = $userActivity->reciever_id;
                                $extraPayLoadData['action_type'] = Config::get('constants.notifications')['2']['title'];
                                $this->sendPush(Config::get('constants.notifications')['2']['msg'], $extraPayLoadData);

                                $res['match'] = true;
                                $user = $this->user->getByIdWithImages($request->user_id);

                                return RESTAPIHelper::response(['data' => $res, 'user' => $user], 200, 'Liked Successfully with a Match !', $this->isBlocked);
                            } else {
                                $user = $this->user->getByIdWithImages($request->user_id);
                                return RESTAPIHelper::response(['data' => $res, 'user' => $user], 200, 'Liked Successfully with No Match !', $this->isBlocked);
                            }
                        } else {
                            return RESTAPIHelper::response([], 404, 'You have already Matched with this User !', $this->isBlocked);
                        }
                    } else {
                        return RESTAPIHelper::response([], 404, 'You have already Liked this User !', $this->isBlocked);
                    }
                    break;
                case 'dislike':
                    if (!$this->userActivity->isDislikeActivityExists($postData)) {
                        if (!$this->userActivity->isMatch($postData)) {
                            //$postData['is_like'] = "0";
                            //$postData['is_boost'] = "0";
                            //$this->userActivity->create($postData);
                            $this->userActivity->updateOrCreate([
                                'sender_id'   => $postData['sender_id'],
                                'reciever_id' => $postData['reciever_id']
                            ], $postData);
                            $user = $this->user->getByIdWithImages($request->user_id);
                            return RESTAPIHelper::response(['user' => $user], 200, 'Dislike Successfully', $this->isBlocked);
                        } else {
                            return RESTAPIHelper::response([], 404, 'You have already Matched with this User !', $this->isBlocked);
                        }
                    } else {
                        return RESTAPIHelper::response([], 404, 'You have already Disliked this User !', $this->isBlocked);
                    }
                    break;
                case 'boost':
                    if (!$this->userActivity->isMatch($postData)) {
                        $userData = $this->user->find($request->user_id);
                        if ($userData->first_boost_date) {
                            $t1 = strtotime(Carbon::now());
                            $t2 = strtotime($userData->first_boost_date);
                            $diff = $t1 - $t2;
                            $hours = intval($diff / (60 * 60));

                            if ($userData->boost_count < 1 && $hours < 168) {   //168 = 1 week

                                $boostData['boost_count'] = $userData->boost_count + 1;
                                $res = $this->user->update($boostData, $request->user_id);

                                if ($res) {
                                    $user = $this->user->getByIdWithImages($request->user_id);
                                    $boostExcute = $this->excuteBoost($postData, $user);
                                    if ($boostExcute['match']) {
                                        return RESTAPIHelper::response(['data' => $boostExcute, 'user' => $user], 200, 'Success', $this->isBlocked);
                                    } else if ($boostExcute['boost']) {
                                        return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
                                    } else {
                                        return RESTAPIHelper::response([], 404, 'Already Boosted, Liked or Disliked', $this->isBlocked);
                                    }
                                } else {
                                    return RESTAPIHelper::response([], 404, 'Not Updated', $this->isBlocked);
                                }
                            } else {
                                if ($hours > 168) {
                                    $boostData['boost_count'] = 1;
                                    $boostData['first_boost_date'] = Carbon::now();
                                    $res = $this->user->update($boostData, $request->user_id);
                                    if ($res) {
                                        $user = $this->user->getByIdWithImages($request->user_id);
                                        $boostExcute = $this->excuteBoost($postData, $user);
                                        if ($boostExcute['match']) {
                                            return RESTAPIHelper::response(['data' => $boostExcute, 'user' => $user], 200, 'Success', $this->isBlocked);
                                        } else if ($boostExcute['boost']) {
                                            return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
                                        } else {
                                            return RESTAPIHelper::response([], 404, 'Already Boosted, Liked or Disliked', $this->isBlocked);
                                        }

                                    } else {
                                        return RESTAPIHelper::response([], 404, 'Not Updated', $this->isBlocked);
                                    }
                                }
                                return RESTAPIHelper::response([], 404, 'Only One Boost Per Week', $this->isBlocked);
                            }
                        } else {
                            $boostData['boost_count'] = 1;
                            $boostData['first_boost_date'] = Carbon::now();
                            $res = $this->user->update($boostData, $request->user_id);
                            if ($res) {

                                $user = $this->user->getByIdWithImages($request->user_id);
                                $boostExcute = $this->excuteBoost($postData, $user);
                                if ($boostExcute['match']) {
                                    return RESTAPIHelper::response(['data' => $boostExcute, 'user' => $user], 200, 'Success', $this->isBlocked);
                                } else if ($boostExcute['boost']) {
                                    return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
                                } else {
                                    return RESTAPIHelper::response([], 404, 'Already Boosted, Liked or Disliked', $this->isBlocked);
                                }
                            } else {
                                return RESTAPIHelper::response([], 404, 'Not Updated', $this->isBlocked);
                            }
                        }
                    } else {
                        return RESTAPIHelper::response([], 404, 'You have already Matched with this User !', $this->isBlocked);
                    }

                    break;
            }


        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    /*Update User Search Preferences*/
    public function updateUserSearchPreference(UpdateSearchPerferenceRequest $request)
    {
        $res = array();

        if ($request->by_location == '1' && $request->by_country == '1') {
            return RESTAPIHelper::response([], 404, 'Country and location cannot be select at a time', $this->isBlocked);
        }

        if ($request->by_location == '1') {
            $res['by_location'] = $request->by_location;
            $res['distance'] = $request->distance;
            $res['by_country'] = '0';
            $res['country'] = null;

        }
        if ($request->by_country == '1') {
            $res['by_country'] = $request->by_country;
            $res['country'] = $request->country;
            $res['by_location'] = '0';
            $res['distance'] = null;
        }
        if ($request->by_age_range == '1') {
            $res['by_age_range'] = $request->by_age_range;
            $res['age_range'] = $request->age_range;
        } else {
            $res['by_age_range'] = $request->by_age_range;
            $res['age_range'] = null;
        }


        if ($request->by_ethnicity == '1') {
            $res['by_ethnicity'] = $request->by_ethnicity;
            $res['ethnicity'] = $request->ethnicity;
        } else {
            $res['by_ethnicity'] = $request->by_ethnicity;
            $res['ethnicity'] = null;
        }
        try {
            if ($this->searchPreferences->findBy('user_id', $request->user_id))
                $res = $this->searchPreferences->updateByUserId($res, $request->user_id);
            else {
                $res['user_id'] = $request->user_id;
                $res = $this->searchPreferences->create($res);
            }

            if ($res) {
                $data = $this->user->getById($request->user_id);
                $this->getUserBlockedStatus($request->user_id);
                return RESTAPIHelper::response(['user' => $data], 200, 'Preference Updated Successfully', $this->isBlocked);

            } else {
                return RESTAPIHelper::response([], 404, 'Preference Not Updated');
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    /*Get User liked and boost profiles*/
    public function getRelativeProfilesByUserId(UserIDRequest $request)
    {
        $params = $request->all();
        $limit = $request->limit;
        $offset = $request->offset;

        if ($limit == "") {
            $limit = Config::get('constants.limit');
        }

        if ($offset == "") {
            $offset = 0;
        }

        if ($offset == 0) $offset = 1;

        $start_limit = ($offset - 1) * $limit;
        $offset = ($start_limit < 0) ? 0 : $start_limit;

        $params['limit'] = $limit;
        $params['offset'] = $offset;

        try {
            $res = $this->user->getILikedBoostedProfiles($params);
            if ($res) {
                $this->getUserBlockedStatus($request->user_id);
                return RESTAPIHelper::response(['user' => $res['data']], 200, 'Profile found successfully.', $this->isBlocked, '', $res['pages']);
            } else {
                return RESTAPIHelper::response([], 404, 'No record found.', $this->isBlocked);
            }

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    /*get my boosters*/
    public function getUserBoosters(UserIDRequest $request)
    {

        $params = $request->all();

        $limit = $request->limit;
        $offset = $request->offset;

        if ($limit == "") {
            $limit = Config::get('constants.limit');
        }

        if ($offset == "") {
            $offset = 0;
        }

        if ($offset == 0) $offset = 1;

        $start_limit = ($offset - 1) * $limit;
        $offset = ($start_limit < 0) ? 0 : $start_limit;

        $params['limit'] = $limit;
        $params['offset'] = $offset;

        try {
            $res = $this->user->getBoostersByUserID($params);

            $this->getUserBlockedStatus($request->user_id);
            if ($res) {
                return RESTAPIHelper::response(['user' => $res['data']], 200, 'Success', $this->isBlocked, '', $res['pages']);
            } else {
                return RESTAPIHelper::response([], 200, 'No Record Found', $this->isBlocked);
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    /*get profiles who like me*/
    public function getWhoLikedMe(UserIDRequest $request)
    {
        $params = $request->all();
        $limit = $request->limit;
        $offset = $request->offset;

        if ($limit == "") {
            $limit = Config::get('constants.limit');
        }

        if ($offset == "") {
            $offset = 0;
        }

        if ($offset == 0) $offset = 1;

        $start_limit = ($offset - 1) * $limit;
        $offset = ($start_limit < 0) ? 0 : $start_limit;

        $params['limit'] = $limit;
        $params['offset'] = $offset;


        try {
            $res = $this->user->profilesWhoLiked($params);
            $this->getUserBlockedStatus($request->user_id);
            if ($res) {
                return RESTAPIHelper::response(['user' => $res['data']], 200, 'Success', $this->isBlocked, '', $res['pages']);
            } else {
                return RESTAPIHelper::response([], 200, 'No Record Found', $this->isBlocked);
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }

    /*user report*/
    public function userReport(UserReportRequest $request)
    {
        $postData = $request->all();

        $postData['sender_id'] = $request->user_id;
        try {
            $this->getUserBlockedStatus($request->user_id);

            if ($this->uReport->create($postData)) {

                $sender_name = $this->user->getNameByID($postData['sender_id']);
                $receiver_name = $this->user->getNameByID($postData['reciever_id']);

                $postData['sender_name'] = $sender_name->full_name;
                $postData['reciever_name'] = $receiver_name->full_name;

                $commonFunction = new Utils();
                /*$toEmail = [];
                $adminEmail = $this->user->getAdminEmail();
                $subAdminEmail = $this->user->getSubAdminEmail();
                $subAdminEmail = array_column($subAdminEmail, 'email');

                $toEmail['toEmail'] = $adminEmail->email;
                $toEmail['ccEmail'] = $subAdminEmail;

                $commonFunction->StartEmailJob($toEmail, $postData);*/
                $commonFunction->StartEmailJob($postData);

                return RESTAPIHelper::response([], 200, 'Report Submitted Successfully', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 200, 'Report Not Submitted', $this->isBlocked);
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 200, 'Internal Server Error', $this->isBlocked);
        }
    }

    /*Rewind user*/
    public function rewindUser(UserIDRequest $request)
    {

        try {
            $userData = $this->user->find($request->user_id);
            $this->getUserBlockedStatus($request->user_id);

            if ($userData->first_rewind_date) {
                $t1 = strtotime(Carbon::now());
                $t2 = strtotime($userData->first_rewind_date);
                $diff = $t1 - $t2;
                $hours = intval($diff / (60 * 60));

                if ($userData->rewind_count < 3 && $hours < 168) {   //168 = 1 week

                    $postData['rewind_count'] = $userData->rewind_count + 1;
                    $res = $this->user->update($postData, $request->user_id);

                    if ($res) {
                        $user = $this->user->getByIdWithImages($request->user_id);
                        return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
                    } else {
                        return RESTAPIHelper::response([], 404, 'Not Updated', $this->isBlocked);
                    }
                } else {
                    if ($hours > 168) {
                        $postData['rewind_count'] = 1;
                        $postData['first_rewind_date'] = Carbon::now();
                        $res = $this->user->update($postData, $request->user_id);
                        if ($res) {
                            $user = $this->user->getByIdWithImages($request->user_id);
                            return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
                        } else {
                            return RESTAPIHelper::response([], 404, 'Not Updated', $this->isBlocked);
                        }

                    }
                    return RESTAPIHelper::response([], 404, 'Only Three Rewinds Per Week', $this->isBlocked);
                }
            } else {
                $postData['rewind_count'] = 1;
                $postData['first_rewind_date'] = Carbon::now();
                $res = $this->user->update($postData, $request->user_id);
                if ($res) {
                    $user = $this->user->getByIdWithImages($request->user_id);
                    return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
                } else {
                    return RESTAPIHelper::response([], 404, 'Not Updated', $this->isBlocked);
                }
            }

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());
        }
    }

    /*Get Notification Count*/
    public function getNotificationCount(UserIDRequest $request)
    {

        try {
            $unreadLikeCount = $this->notification->getUnReadCount($request->user_id, $action_type = "like");
            $unreadBoostCount = $this->notification->getUnReadCount($request->user_id, $action_type = "is_boost");
            $unreadAdminNotiCount = $this->notification->getUnReadCount($request->user_id, $action_type = "general");
            $res['unReadLike'] = $unreadLikeCount;
            $res['unReadBoost'] = $unreadBoostCount;
            $res['unReadAdminMessage'] = $unreadAdminNotiCount;
            $this->getUserBlockedStatus($request->user_id);
            return RESTAPIHelper::response(['count' => $res], 200, 'Success', $this->isBlocked);

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());

        }
    }

    /*Mark All notification Read*/
    public function markNotificationRead(UserIDRequest $request)
    {
        try {
            $user = $this->user->find($request->user_id);
            $user->notifications()->where('action_type', '!=', 'general')->update(['is_read' => 1]);
            if ($user) {
                $unreadLikeCount = $this->notification->getUnReadCount($request->user_id, $action_type = "like");
                $unreadBoostCount = $this->notification->getUnReadCount($request->user_id, $action_type = "is_boost");
                $res['unReadLike'] = $unreadLikeCount;
                $res['unReadBoost'] = $unreadBoostCount;
                $this->getUserBlockedStatus($request->user_id);
                return RESTAPIHelper::response(['count' => $res], 200, 'Success', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'Something Went Wrong', $this->isBlocked);
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());
        }

    }

    /*Get Thread Id & User Object (Same As Node Server Api response for Chat)*/
    public function getChatThreadIdAndUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|int|exists:users,id',
            'reciever_id' => 'required|int|exists:users,id'
        ]);
        if ($validator->fails()) {
            return RESTAPIHelper::response([], 404, $validator->errors()->first());
        }
        $params = $request->all();
        try {
            $this->getUserBlockedStatus($request->user_id);
            $thread_id = $this->threadUser->getThreadIdByUsersID($params);
            //$user = $this->user->getByIdWithImages($params['reciever_id']);
            $user = $this->user->getReceiverProfileById($params);
            $user['user_id'] = (int)$params['reciever_id'];
            $user['thread_id'] = (int)$thread_id->thread_id;
            if ($thread_id && $user) {
                return RESTAPIHelper::response(['user' => $user], 200, 'Success', $this->isBlocked);
            }
            return RESTAPIHelper::response([], 404, 'Thread Not Found', $this->isBlocked);

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());
        }
    }


    /*Unmatch User*/

    public function UnMatched(UnMatchActivityRequest $request)
    {
        try {
            $postData = $request->all();
            $postData['sender_id'] = $postData['user_id'];
            DB::beginTransaction();
            if ($this->userActivity->isUserUnMatch($postData)) {
                $user = $this->userActivity->deleteUnMatchUser($postData);
                try {
                    $this->thread->delete($postData['thread_id']);
                    $this->threadUser->deleteByThreadId($postData);
                    $this->chat->deleteByThreadId($postData);
                } catch (\Exception $e) {
                    DB::rollback();
                    return RESTAPIHelper::response([], 500, $e->getMessage());
                }
                DB::commit();
                return RESTAPIHelper::response(['user' => $user], 200, 'UnMatched Successfully', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'Already UnMatched', $this->isBlocked);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }

    }

    public function unMatchUserByThreadID(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|int|exists:users,id',
            'thread_id' => 'required|int|exists:threads,id'
        ]);
        if ($validator->fails()) {
            return RESTAPIHelper::response([], 404, $validator->errors()->first());
        }
        try {
            $this->thread->delete($params['thread_id']);
            $this->threadUser->deleteByThreadId($params['thread_id']);
            $this->chat->deleteByThreadId($params['thread_id']);

            return RESTAPIHelper::response([], 200, 'Success');

        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage());
        }
    }

    /*Dealing Pending Request */
    protected function pendingRequests(array $array)
    {
        foreach ($array as $act) {
            $this->userActivity->updateOrCreate(
                [
                    'sender_id'   => $act['sender_id'],
                    'reciever_id' => $act['reciever_id']
                ],
                $act
            );
            if ($this->userActivity->isMatch($act)) {

                /*send push to reciever_id*/
            }
        }

    }

    /*Create thread for of user id and reciever id , where user id is created_by */
    private function createThread($parm)
    {
        $res = array();
        $res['thread_id'] = 0;
        $thread = $this->thread->create(['created_by' => $parm['sender_id']]);
        if ($thread) {
            $thread->users()->sync([$parm['sender_id'], $parm['reciever_id']]);
            return $res['thread_id'] = $thread->id;
        } else {
            return $res;
        }
    }

    /*Send push only in like or is_boost scenario */
    private function SendPush($msg, $extaPayLoadData)
    {
        try {
            $pushNotificationData = array();
            $notifier = $this->udevice->getEnabledDeviceToken($extaPayLoadData);

            /*Get Unread Count*/
            /*$unreadLikeCount = $this->notification->getUnReadCount($extaPayLoadData['user_id'], $action_type = "like");
            $unreadBoostCount = $this->notification->getUnReadCount($extaPayLoadData['user_id'], $action_type = "is_boost");
            $unreadAdminNotiCount = $this->notification->getUnReadCount($extaPayLoadData['user_id'], $action_type = "general");
            // FIXME: Use Models and Repositories to replace below query.
            $res = DB::select('SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=?) OR (ur.sender_id=c.sender_id AND reciever_id=?) WHERE cr.type <> \'read_by_user\' AND c.sender_id <> ? AND tu.user_id = ? AND c.deleted_at IS NULL AND (c.deleted_by <> ? OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))', [$extaPayLoadData['user_id'], $extaPayLoadData['user_id'], $extaPayLoadData['user_id'], $extaPayLoadData['user_id'], $extaPayLoadData['user_id']]);

            $unreadMsgCount = $res ? $res[0]->unread_messages : 0;
            $badge = $unreadLikeCount + $unreadBoostCount + $unreadMsgCount + $unreadAdminNotiCount;*/

            foreach ($notifier as $key => $value):


                /*Get Unread Count*/
                $unreadLikeCount = $this->notification->getUnReadCount($value['id'], $action_type = "like");
                $unreadBoostCount = $this->notification->getUnReadCount($value['id'], $action_type = "is_boost");
                $unreadAdminNotiCount = $this->notification->getUnReadCount($value['id'], $action_type = "general");
                // FIXME: Use Models and Repositories to replace below query.
                $res = DB::select('SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=?) OR (ur.sender_id=c.sender_id AND reciever_id=?) WHERE cr.type <> \'read_by_user\' AND c.sender_id <> ? AND tu.user_id = ? AND c.deleted_at IS NULL AND (c.deleted_by <> ? OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))', [$value['id'], $value['id'], $value['id'], $value['id'], $value['id']]);

                $unreadMsgCount = $res ? $res[0]->unread_messages : 0;
                $badge = $unreadLikeCount + $unreadBoostCount + $unreadMsgCount + $unreadAdminNotiCount;


                $pushNotificationData[] = ['user_id' => $value['id'], 'device_type' => $value['device_type'], 'device_token' => $value['device_token'], 'badge_count' => $badge];
            endforeach;

            if ($pushNotificationData) {
                sendPushNotifications($msg, $pushNotificationData, $extaPayLoadData, 0);
            }
        } catch (Exception $e) {
            dd($e->getTraceAsString());
        }

    }

    /*Excute Post*/
    private function excuteBoost($postData, $user)
    {
        $res['match'] = false;
        $res['boost'] = false;

        //if (!$this->userActivity->isActivityExists($postData)) {
        //$postData['is_boost'] = "1";
        $userActivity = $this->userActivity->updateOrCreate([
            'sender_id'   => $postData['sender_id'],
            'reciever_id' => $postData['reciever_id']
        ], $postData);

        $isMatch = $this->userActivity->isMatch($postData);

        if ($isMatch) {
            /*Create Notification For Receiver*/
            $notificationData['message'] = Config::get('constants.notifications')['2']['msg'];
            $notificationData['action_type'] = Config::get('constants.notifications')['2']['title'];
            $notificationData['ref_id'] = $userActivity->id;
            $notification = $this->notification->create($notificationData);
            $notification->users()->attach($userActivity->reciever_id);

            $thread_id = $this->createThread($postData);
            $extraPayLoadData['user_id'] = $userActivity->reciever_id;
            $extraPayLoadData['thread_id'] = $thread_id;
            $extraPayLoadData['action_type'] = Config::get('constants.notifications')['2']['title'];
            $this->sendPush(Config::get('constants.notifications')['2']['msg'], $extraPayLoadData);
            $res['match'] = true;
            $res['boost'] = true;
            return $res;
        } else {
            /*Create Notification For Receiver*/
            $notificationData['message'] = Config::get('constants.notifications')['3']['msg'];
            $notificationData['action_type'] = Config::get('constants.notifications')['3']['title'];
            $notificationData['ref_id'] = $userActivity->id;
            $notification = $this->notification->create($notificationData);
            $notification->users()->attach($userActivity->reciever_id);

            $extraPayLoadData['user_id'] = $userActivity->reciever_id;
            $extraPayLoadData['thread_id'] = '0';
            $extraPayLoadData['action_type'] = Config::get('constants.notifications')['3']['title'];
            $this->sendPush(Config::get('constants.notifications')['3']['msg'], $extraPayLoadData);
            $res['match'] = false;
            $res['boost'] = true;
            return $res;
        }

        //return true;
        #   return RESTAPIHelper::response(['user' => $user], 200, 'Boost Successfully', $this->isBlocked);
        /*} else {
            return $res;
            //return false;
            #  return RESTAPIHelper::response([], 404, 'Already Boosted, Liked or Disliked', $this->isBlocked);
        }*/
    }


}