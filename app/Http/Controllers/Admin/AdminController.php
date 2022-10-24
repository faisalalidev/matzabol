<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminPasswordRequest;
use App\Http\Requests\Admin\AdminsettingRequest;
use App\Repositories\ContactusRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\UserReportRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubAdminRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Flash;
use Hash;
use Alert;
use Illuminate\Support\Facades\Redirect;
use UxWeb\SweetAlert\SweetAlert;

class AdminController extends Controller
{

    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function index(Request $request, UserRepository $user, UserReportRepository $userReport, ContactusRepository $contactus, SubAdminRepository $subAdminRepository, EmailTemplateRepository $templateRepository)
    {

        $modules = array(
            "users"        => ['count' => count($user->getDataTable()), 'icon' => "fa fa-users", 'bg' => "primary"],
            "user-reports" => ['count' => count($userReport->getDataTable()), 'icon' => "fa fa-flag-checkered", 'bg' => "red"],
            "contact-us"   => ['count' => count($contactus->getDataTable()), 'icon' => "fa fa-comments", 'bg' => "green"],
        );

        if (Auth::user()->role_id == 1) {
            $modules += array("sub-admin" => ['count' => count($subAdminRepository->getDataTable()), 'icon' => "fa fa-group", 'bg' => "yellow"]);
            $modules += array("email-template" => ['count' => count($templateRepository->getDataTable()), 'icon' => "fa fa-envelope", 'bg' => "primary"]);
        }

        Session::put('modules', $modules);
        //$test = 'abs';
        $admin = Auth::user();
        return view('admin.dashboard', ['admin' => $admin, 'admin_data' => $request['admin_data']]);
    }

    public function login(Request $request)
    {
        $email = Input::get('email');
        $password_input = Input::get('password');

        session(['timezone' => $request->timezone]); // saving to session

        if (Auth::attempt(['email' => $email, 'password' => $password_input, 'role_id' => 1])) {
            //dd(Session::all());
            Session::put('full_authenticated', true);
            //dd(Session::all());
            return redirect('admin/dashboard');

        } elseif (Auth::attempt(['email' => $email, 'password' => $password_input, 'role_id' => 3])) {
            if (Auth::user()->status == '1') {
                //dd(Session::all());
                Session::put('full_authenticated', true);
                //dd(Session::all());
                return redirect('admin/dashboard');

            } else {
                Auth::logout();
                Session::flush();
                Flash::error('Your account has been blocked by Admin !');
                return redirect('admin/login');
            }

        } else {
            Flash::error('Email or Password is incorrect');
            return redirect('admin/login');
        }
    }

    public function changePassword()
    {
        return view('admin.settings.adminsettings', ['module' => 'Admin Settings']);
    }

    public function updatePassword(AdminPasswordRequest $request)
    {
        $user = $this->user->find(Auth::id());
        $updateData = $request->all();
        $hashedPassword = $user->password;
        if (Hash::check($updateData['old'], $hashedPassword)) {
            if (strcmp($updateData['password'], $updateData['old']) != 0) {
                $newPassword = bcrypt($updateData['password']);
                $update['password'] = $newPassword;
                if ($this->user->update($update, $user->id)) {
                    //return redirect('/')->with('success', 'Password Changed Successfully !');
                    //return Redirect::to('/');
                    alert()->success('Password Changed Successfully.', 'Password Changed !');
                    return redirect()->route('admin.dashboard');
                    //return Redirect::to('/');
                    //Alert::message('Password Changed Successfully.', 'Success!');
                    //return Redirect::to('/');
                    //return redirect('/')->with('success', 'Profile updated!');
                    //$request->session()->flash('success', 'Your password has been changed.');
                    //return view('admin.login');
                }
            } else {
                Flash::error('Current Password and New Password cannot be same');
                return Back();
            }

        } else {
            Flash::error('Current Password is incorrect');
            return Back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
