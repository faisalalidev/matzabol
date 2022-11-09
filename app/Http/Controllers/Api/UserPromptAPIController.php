<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Model\UserPrompt;
use App\Models\UserImage;
use App\Repositories\UserPromptRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPromptAPIController extends Controller
{
    protected $userPromptrepository, $user;
    public function __construct(UserPromptRepository $userPromRepo,UserRepository $userService)
    {
        $this->userPromptrepository = $userPromRepo;
        $this->user = $userService;
    }

    public function index(Request $request)
    {

        return $this->userPromptrepository->getDataTable($request->user_id);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('video')) {
//            dd($request->video);
            $filename = $request->video->store('users');
        }
        $res = UserPrompt::create([
            'user_id'    => $request->user_id,
            'prompt_id'      => $request->prompt_id,
            'text'      => $request->text,
            'video'      => isset($filename) ?$filename : null ,
        ]);
        return RESTAPIHelper::response(['user' => $this->user->getByIdWithImages($request->user_id)], 200, 'Insert successfully.', $this->isBlocked);

    }
}
