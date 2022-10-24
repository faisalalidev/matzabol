<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Libraries\APIResponse;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, APIResponse;

/*    protected $sessId;
    protected $myCart;*/

    protected $isBlocked = 0;

    public function __construct()
    {
        /*$this->sessId =  session('sess_id');
        if ($this->sessId == "")
        {
            Session::put('sess_id', session()->getId());
            $this->sessId = session('sess_id');
        }*/
    }
    protected function unlinkFile($file, $path)
    {
        if ($file)
        {
            @unlink($path . $file);
            @unlink($path .'thump_'. $file);
            @unlink($path .'small_'. $file);
            @unlink($path .'round_'. $file);
        }
    }
}
