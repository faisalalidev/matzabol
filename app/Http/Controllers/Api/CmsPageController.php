<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Http\Requests\Api\CmsPageRequest;
use App\Http\Controllers\Controller;
use App\Repositories\CmsPageRepository;


class CmsPageController extends Controller
{
    //
    protected $cmsPage;

    public function __construct(CmsPageRepository $cmsPage)
    {
        $this->cmsPage = $cmsPage;
    }

    public function getByType(CmsPageRequest $request)
    {
        try {
            $res = $this->cmsPage->getByType($request->type);
            if ($res) {
                return RESTAPIHelper::response(['cmspage' => $res], 200, 'Success', $this->isBlocked);
            } else {
                return RESTAPIHelper::response([], 404, 'Error', $this->isBlocked);
            }
        } catch (\Exception $e) {
            return RESTAPIHelper::response([], 500, $e->getMessage(), $this->isBlocked);
        }
    }
}
