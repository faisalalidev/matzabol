<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CmspageAddRequest;
use App\Repositories\CmsPageRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use Alert;
use Illuminate\Support\Facades\Validator;

class CmspageController extends Controller
{
    protected $request, $cms;

    public function __construct(CmsPageRepository $cms, Request $request)
    {
        $this->cms = $cms;
        $this->request = $request;
    }

    public function getDataTable()
    {
        $cmspage = $this->cms->getDataTable();
        $data = Datatables::of($cmspage)
            ->addColumn('action', function ($cms) {
                return '<a href="' . url('admin/cms-page/detail/' . $cms->id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" title="View Details"><i class="fa fa-eye" aria-hidden="true"></i></a>'
                    . ' <a href=' . url('/admin/cms-page/edit/' . $cms->id) . ' class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'
                    . ' <a href="' . url('admin/cms-page/destroy/' . $cms->id) . '" class="btn btn-xs btn-danger cus_status" data-msg="Are you sure you want to delete?" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
            })
            ->editColumn('type', function ($cms) {
                if ($cms->type == "policies") {
                    return ucwords('Privacy Policy');
                } else if ($cms->type == "terms") {
                    return ucwords('Terms & Conditions');
                }
            })
            ->editColumn('updated_at', function ($cms) {
                return date('d-m-Y h:i:s', strtotime($cms->updated_at->timezone(session('timezone'))));
            })
            ->rawColumns(['action', 'type', 'updated_at'])
            ->removeColumn('id')
            ->make(true);

        return $data;
    }

    public function manageCmspages($filter = 'list', $id = NULL)
    {

        switch ($filter) {
            case 'list':
                return view('admin.cmspages.cmspages', ['module' => "CMS Pages"]);
                break;
            case 'create':
                return view('admin.cmspages.add_cmspage', ['module' => "CMS Pages"]);
                break;
            case 'edit':
                return view('admin.cmspages.edit_cmspage', ['module' => "CMS Pages", 'cms' => $this->cms->find($id)]);
                break;
            case 'detail':
                $cms = $this->cms->find($id);
                return view('admin.cmspages.detail', ['cms' => $cms, 'module' => "CMS Pages"]);
                break;
            case 'destroy':
                $this->cms->delete($id);
                Alert::success('Succesfully Deleted!');
                return redirect('admin/' . $this->request->segment(2));
                break;
            default:
                abort(404);
        }
    }

    public function store(CmspageAddRequest $request)
    {

        $postData = $request->all();

        $this->cms->create($postData);

        Alert::success('Succesfully Created!');
        return redirect('admin/cms-page');
    }


    public function update(Request $request, $id)
    {

        $parm = $request->all();
        $this->cms->update($parm, $id);

        Alert::success('Succesfully Updated!');
        return redirect('admin/cms-page');
    }

}
