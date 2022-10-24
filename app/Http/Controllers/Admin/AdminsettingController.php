<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminsettingRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Alert;
use Yajra\Datatables\Datatables;

class AdminsettingController extends Controller
{
    protected $service;
    protected $request;
    protected $jobService;

    public function __construct(Request $request) {

        $this->request = $request;

    }

    /*public function getDataTable() {
        $jobs = $this->jobService->getDataTable();
        //dd($categories);
        $data = Datatables::of($jobs)
            ->addColumn('action', function ($job) {
                
            })

            ->rawColumns(['action'])
            ->removeColumn('id')
            ->make(true);

        return $data;
    }*/

    /*public function index(){
        $adminSetting = $this->service->getData(1);
 #       return $adminSetting->toJson();
        return view('admin.settings.adminsettings',[ 'module' => 'Admin Settings' , 'adminsettings' => $adminSetting]);
    }*/
/*
    public function update(AdminsettingRequest $request, $id){
        $postData = $request->all();
        if ($request->hasFile('logo')) {

            $path = Config::get('constants.front.default.siteSetting');
            $data = $this->service->getData($id);
            $file = $request->file('logo');
            $fileName = 'logo' . '.' . $request->file('logo')->getClientOriginalExtension();
            $destinationPath = public_path(Config::get('constants.front.default.siteSetting'));
            $file->move($destinationPath, $fileName);
            $postData['logo'] = $fileName;
        }

        if ($request->hasFile('favicon')) {

            $file = $request->file('favicon');
            $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('favicon')->getClientOriginalExtension();
            $destinationPath = public_path(Config::get('constants.front.default.siteSetting'));
            $file->move($destinationPath, $fileName);
            $postData['favicon'] = $fileName;
        }
        $path = public_path(Config::get('constants.front.default.siteSetting'));
        $data = $this->service->getData($id);
        $this->service->updateData($postData, $id);
        if($this->service->updateData($postData, $id) &&  $request->hasFile('logo') ){
            $this->unlinkFile($data['logo'],$path);
        }
        if($this->service->updateData($postData, $id) &&  $request->hasFile('favicon') ){
            $this->unlinkFile($data['favicon'],$path);
        }

        Alert::success('Succesfully Updated!');
        return Redirect::back();
    }*/
}
