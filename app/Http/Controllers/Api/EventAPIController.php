<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Repositories\EventRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventAPIController extends Controller
{
    //
    protected $eventRepo;
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepo = $eventRepository;
    }

    public function index(Request $request)
    {

       return RESTAPIHelper::response(['event' => $this->eventRepo->getDataTable()], 200, 'Event Fetch successfully.', $this->isBlocked);

    }
}
