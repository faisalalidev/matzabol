<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ContactusRepository;
use App\Http\Controllers\Controller;
use DataTables;
use Alert;
use Illuminate\Support\Facades\Redirect;

class ContactusController extends Controller
{
    protected $contactUs;

    public function __construct(ContactusRepository $contactUs)
    {
        $this->contactUs = $contactUs;
    }

    public function getDataTable()
    {
        $contacts = $this->contactUs->getDataTable();

        $data = Datatables::of($contacts)
            ->editColumn('user_name', function ($contact) {
                return '<a href=' . url('/admin/users/detail/' . $contact->user_id) . ' target="_blank">' . ucwords($contact->user_name) . '</a>';
                //return ucwords($contact->user_name);
            })
            ->editColumn('message', function ($contact) {
                return '<span style="word-break: break-all">' . json_decode('"' . $contact->message . '"') . '</span>';
            })
            ->editColumn('created_at', function ($contact) {
                return date('d-m-Y h:i:s', strtotime($contact->created_at->timezone(session('timezone'))));
            })
            ->rawColumns(['user_name', 'message', 'created_at'])//'action',
            ->make(true);

        return $data;
    }

    public function index()
    {
        return view('admin.contactus.contactus', ['module' => 'Contact Us']);
    }

    public function delete($id)
    {
        if ($this->contactUs->delete($id)) {
            Alert::success('Successfully Deleted');
            return Redirect::back();
        }
    }
}