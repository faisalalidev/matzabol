<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserReportRepository;
use App\Http\Controllers\Controller;
use DataTables;
use Alert;
use Illuminate\Support\Facades\Redirect;

class UserReportController extends Controller
{
    protected $userReport;

    public function __construct(UserReportRepository $userReport)
    {
        $this->userReport = $userReport;
    }

    public function getDataTable()
    {
        $reports = $this->userReport->getDataTable();

        $data = Datatables::of($reports)
            ->addColumn('action', function ($report) {
                return '<a data-msg="Are you sure you want to block ' . ucwords($report->reciever_name) . '?" href="' . url('admin/users/block/' . $report->reciever_id) . '" class="btn btn-xs btn-danger cus_status">Block User</a>'
                    . ' <a data-msg="Are you sure you want to remove this user from report?" href="' . url('admin/report/delete/' . $report->id) . '" class="btn btn-xs btn-warning cus_status">Report Delete</a>';
            })
            ->editColumn('type', function ($report) {
                return ucwords(str_replace("_", " ", $report->type));
            })
            ->editColumn('sender_name', function ($report) {
                return '<a href=' . url('/admin/users/detail/' . $report->sender_id) . ' target="_blank">' . ucwords($report->sender_name) . '</a>';
                //return ucwords($report->sender_name);
            })
            ->editColumn('reciever_name', function ($report) {
                return '<a href=' . url('/admin/users/detail/' . $report->reciever_id) . ' target="_blank">' . ucwords($report->reciever_name) . '</a>';
                //return ucwords($report->reciever_name);
            })
            ->editColumn('created_at', function ($report) {
                return date('d-m-Y h:i:s', strtotime($report->created_at->timezone(session('timezone'))));
            })
            ->rawColumns(['type', 'sender_name', 'reciever_name', 'action', 'created_at'])//'action',
            ->make(true);

        return $data;
    }

    public function index()
    {
        return view('admin.report.report', ['module' => 'User Reports']);
    }

    public function delete($id)
    {
        if ($this->userReport->delete($id)) {
            Alert::success('Successfully Deleted');
            return Redirect::back();
        }
    }
}
