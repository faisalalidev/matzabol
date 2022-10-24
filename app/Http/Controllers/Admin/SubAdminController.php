<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SubAdminRepository;
use App\Models\SubAdmin;
use App\Http\Requests\Admin\SubAdminCreateRequest;
use App\Http\Requests\Admin\SubAdminUpdateRequest;
use Datatables;
use Alert;

class SubAdminController extends Controller
{

    protected $subAdminService;
    protected $request;

    public function __construct(SubAdminRepository $subAdminService, Request $request)
    {
        $this->subAdminService = $subAdminService;
        $this->request = $request;
    }

    public function getDataTable()
    {
        $subAdmins = $this->subAdminService->getDataTable();
        $data = Datatables::of($subAdmins)
            ->addColumn('action', function ($subAdmin) {
                return '<a href=' . url('/admin/sub-admin/edit/' . $subAdmin->id) . ' class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'
                    . ' <a href="' . url('admin/sub-admin/destroy/' . $subAdmin->id) . '" class="btn btn-xs btn-danger cus_status" data-msg="Are you sure you want to delete/cancel?" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>'
                    . ' <a href="' . url('admin/sub-admin/detail/' . $subAdmin->id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" title="View Details"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            })
            ->editColumn('status', function ($subAdmin) {
                if ($subAdmin->status == 1) return '<span class="label label-success">Active</span>';
                else return '<span class="label label-danger">Deactive</span>';
            })
            ->rawColumns(['action', 'status'])
            ->removeColumn('id')
            ->make(true);

        return $data;
    }

    public function manageEvents($filter = 'list', $id = NULL)
    {
        switch ($filter) {
            case 'list':
                return view('admin.subadmins.subadmins', ['module' => "Sub Admin"]);
                break;
            case 'create':
                return view('admin.subadmins.add_subadmin', ['module' => "Sub Admin"]);
                break;
            case 'edit':
                return view('admin.subadmins.edit_subadmin', ['module' => "Sub Admin", 'subadmin' => $this->subAdminService->getData($id)]);
                break;
            case 'detail':
                $subAdmins = $this->subAdminService->getData($id);
                return view('admin.subadmins.detail', ['subadmin' => $subAdmins, 'module' => "Sub Admin"]);
                break;
            case 'destroy':
                SubAdmin::destroy($id);
                Alert::success('Succesfully Deleted!');
                return redirect('admin/' . $this->request->segment(2));
                break;
            default:
                abort(404);
        }
    }

    public function store(SubAdminCreateRequest $request)
    {
        $postData = $request->all();
        $encryptPass = bcrypt($postData['password']);
        $postData['password'] = $encryptPass;
        $postData['role_id'] = 3;
        $this->subAdminService->setData($postData);
        Alert::success('Succesfully Created!');
        return redirect('admin/sub-admin');
    }

    public function update(SubAdminUpdateRequest $request, $id)
    {
        $postData = $request->all();
        if ($postData['password']) {
            $encryptPass = bcrypt($postData['password']);
            $postData['password'] = $encryptPass;
        } else {
            unset($postData['password']);
        }
        $postData['role_id'] = 3;
        $this->subAdminService->updateData($postData, $id);
        Alert::success('Succesfully Updated!');
        return redirect('admin/sub-admin');
    }

}
