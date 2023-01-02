<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InterestCreateRequest;
use App\Repositories\EventRepository;
use App\Repositories\InterestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Datatables;
use Alert;
class EventController extends Controller
{
    public function __construct(EventRepository $event )
    {
        $this->eventRepository = $event;
    }

    public function store(InterestCreateRequest $request)
    {
        $postData = $request->all();
        $this->eventRepository->setData($postData);
        Alert::success('Succesfully Created!');
        return redirect('admin/events');
    }

    public function show()
    {
        
    }

    public function edit()
    {
        
    }
    public function update()
    {
        
    }

    public function destroy()
    {
        
    }

    public function getDataTable()
    {
        $interest = $this->eventRepository->getDataTable();
        $data = Datatables::of($interest)
//            ->editColumn('user_name', function ($contact) {
//                return '<a href=' . url('/admin/users/detail/' . $contact->user_id) . ' target="_blank">' . ucwords($contact->user_name) . '</a>';
//                //return ucwords($contact->user_name);
//            })
//            ->editColumn('message', function ($contact) {
//                return '<span style="word-break: break-all">' . json_decode('"' . $contact->message . '"') . '</span>';
//            })
//            ->editColumn('created_at', function ($contact) {
//                return date('d-m-Y h:i:s', strtotime($contact->created_at->timezone(session('timezone'))));
//            })
//            ->rawColumns(['name', 'icon', 'created_at'])//'action',
            ->make(true);

        return $data;
    }

    public function manageEvent($filter = 'list', $id = Null)
    {

        switch ($filter) {
            case 'list':
                return view('admin.events.events', ['module' => 'Events']);
                break;
            case 'create':
                return view('admin.events.add_event', ['module' => "Events"]);
                break;
//            case 'active':
//                $this->userService->updateStatus('1', $id);
//                Alert::success('User Activated');
//                return Redirect::back();
//                break;
//            case 'block':
//                $this->userService->updateStatus('0', $id);
//                Alert::success('User Blocked');
//                return Redirect::back();
//                break;
//            case 'detail':
//                $user['info'] = $this->userService->getDetailsById($id);
//                $user['like'] = $this->userService->getUserLikesProfiles($id);
//                $user['dislike'] = $this->userService->getUserDislikesProfiles($id);
//                $user['reportedby'] = $this->userService->getUserReportedByProfiles($id);
//                $user['boost'] = $this->userService->getUserBoostProfiles($id);
//                $user['threads'] = $this->userService->getUserThreads($id);
//                if (!$user['info']) {
//                    Alert::error('User not found, may be already deleted');
//                    return Redirect::back();
//                }
//                return view('admin.users.detail', ['user' => $user, 'module' => "User"]);
//                break;
//            case 'chathistory':
//                $chat = $this->chat->getThreadChat($id);

//                return view('admin.users.chat', ['chat' => $chat, 'module' => "Chat History"]);
//                break;
            case 'delete':
                $this->eventRepository->delete($id);
                Alert::success('Event Successfully Deleted !');
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
