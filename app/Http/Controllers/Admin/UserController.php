<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\ChatRepository;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Datatables;
use Alert;

class UserController extends Controller
{
    protected $userService;
    protected $request;
    protected $chat;

    public function __construct(UserRepository $userService, Request $request,ChatRepository $chats)
    {
        $this->userService = $userService;
        $this->request = $request;
        $this->chat = $chats;
    }

    public function getDataTable()
    {
        $users = $this->userService->getDataTable();

        return Datatables::of($users)
//            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                //$selectBox = '';
                $selectBox = '<select class="select_status" name="process"><option value="">-User Status-</option>';
                if ($user->status == 0) {
                    $selectBox .= '<option value="1" data-msg="Are you sure you want to remove from blocked list?" data-url="' . url('admin/users/active/' . $user->id) . '" >Activate User</option>';

                } else if ($user->status == 1) {
                    $selectBox .= '<option value="0" data-msg="Are you sure you want to block user?" data-url="' . url('admin/users/block/' . $user->id) . '" >Block User</option>';
                }
                $selectBox .= '</select>';

                $selectBox .= ' <a data-msg="Are you sure you want to delete this user?" href="' . url('admin/users/delete/' . $user->id) . '" class="btn btn-xs btn-danger delete_user" data-toggle="tooltip" title="Delete User"><i class="fa fa-trash" aria-hidden="true"></i></a>';

                return $selectBox . ' <a href="' . url('admin/users/detail/' . $user->id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" title="View Details"><i class="fa fa-eye" aria-hidden="true"></i></a>';

            })
            ->editColumn('full_name', function ($user) {
                if ($user->reported == 1) {
                    return '<span><i class="fa fa-flag" aria-hidden="true" style="color: #46b8da" title="Reported"></i> ' . $user->full_name . '</span>';
                }
                return '<span>' . $user->full_name . '</span>';
            })
            ->editColumn('dob', function ($user) {
                $dob = date("d-F-Y", strtotime($user->dob));
                return '<span>' . $dob . '</span>';
            })
            ->editColumn('status', function ($user) {
                if ($user->status == 1)
                    return '<span class="label label-success">Active</span>';
                else
                    return '<span class="label label-danger">Blocked</span>';
            })
            ->rawColumns(['action', 'status', 'dob', 'full_name'])
            //->removeColumn('id')
            ->make(true);
    }

    public function manageUser($filter = 'list', $id = Null)
    {
        switch ($filter) {
            case 'list':
                return view('admin.users.users', ['module' => 'Users']);
                break;
            case 'active':
                $this->userService->updateStatus('1', $id);
                Alert::success('User Activated');
                return Redirect::back();
                break;
            case 'block':
                $this->userService->updateStatus('0', $id);
                Alert::success('User Blocked');
                return Redirect::back();
                break;
            case 'detail':
                $user['info'] = $this->userService->getDetailsById($id);
                $user['like'] = $this->userService->getUserLikesProfiles($id);
                $user['dislike'] = $this->userService->getUserDislikesProfiles($id);
                $user['reportedby'] = $this->userService->getUserReportedByProfiles($id);
                $user['boost'] = $this->userService->getUserBoostProfiles($id);
                $user['threads'] = $this->userService->getUserThreads($id);
                if (!$user['info']) {
                    Alert::error('User not found, may be already deleted');
                    return Redirect::back();
                }
                return view('admin.users.detail', ['user' => $user, 'module' => "User"]);
                break;
            case 'chathistory':
                $chat = $this->chat->getThreadChat($id);

                return view('admin.users.chat', ['chat' => $chat, 'module' => "Chat History"]);
                break;
            case 'delete':
                $this->userService->delete($id);
                Alert::success('User Successfully Deleted !');
                return Redirect::back();
                break;
            default:
                abort(404);
        }

    }

    /* public function update(Request $request)
     {
         $this->userService->updateAdminProfile($request->all(),Auth::id());

         Alert::success('Successfully Updated!');
         return redirect('admin/myprofile');

     }*/

    /*  public function updatepassword(AdminPasswordRequest $request){
          $user = $this->userService->getById(Auth::id());

          $updateData = $request->all();
          $hashedPassword = $user->password;

          if (Hash::check($updateData['old'], $hashedPassword))
          {
              $newPassword = bcrypt($updateData['password']);
              $updateData['password'] = $newPassword;

              if($this->userService->updateAdminPass($updateData,$user->id))
              {   $request->session()->flash('success', 'Your password has been changed.');
                  return view('admin.login');
              }
          }
          else
          { $request->session()->flash('failure', 'Old Password Incorrect');
             return Back();
          }
      }*/

}
