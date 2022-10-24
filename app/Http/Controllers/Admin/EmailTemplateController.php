<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\EmailTemplateRepository;
use App\Models\EmailTemplate;
use Datatables;
use Alert;

class EmailTemplateController extends Controller
{
    protected $templateService;
    protected $request;
    protected $userRepository;

    public function __construct(EmailTemplateRepository $templateRepository, Request $request, UserRepository $userRepository)
    {
        $this->templateService = $templateRepository;
        $this->request = $request;
        $this->userRepository = $userRepository;
    }

    public function getDataTable()
    {
        $subAdmins = $this->templateService->getDataTable();
        $data = Datatables::of($subAdmins)
            ->addColumn('action', function ($subAdmin) {
                return '<a href=' . url('/admin/email-template/edit/' . $subAdmin->id) . ' class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'
                    . ' <a href="' . url('admin/email-template/detail/' . $subAdmin->id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" title="View Details"><i class="fa fa-eye" aria-hidden="true"></i></a>';
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
                return view('admin.email_template.email_template', ['module' => "Email Template"]);
                break;
            case 'edit':
                $users = $this->userRepository->getSubAdmin();
                return view('admin.email_template.edit_email_template', ['module' => "Email Template", 'template' => $this->templateService->getData($id), 'users' => $users]);
                break;
            case 'detail':
                $template = $this->templateService->getData($id);
                return view('admin.email_template.detail', ['template' => $template, 'module' => "Email Template"]);
                break;
            default:
                abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        $postData = $request->all();

        $emailTemplate = $this->templateService->find($id);

        if (empty($emailTemplate)) {
            Flash::error('Email Template not found');
            return redirect('admin/email-template');
        }

        $update = $this->templateService->updateData($postData, $id);
        if ($update) {
            if (isset($postData['users'])) {
                $emailTemplate->users()->attach($postData['users']);
            }
        }
        Alert::success('Succesfully Updated!');
        return redirect('admin/email-template');
    }

}
