<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Models\EventJoin;
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

    public function show($id)
    {
        return RESTAPIHelper::response(['event' =>  $this->eventRepo->getEvent($id)], 200, 'Event Fetch successfully.', $this->isBlocked);


    }

    public function join(Request $request)
    {
        $eventJoin['user_id']= $request->user_id;
        $eventJoin['event_id']= $request->event_id;
        $event = EventJoin::create($eventJoin);
        return RESTAPIHelper::response(['event' => $event], 200, 'Event Fetch successfully.', $this->isBlocked);
    }
}
