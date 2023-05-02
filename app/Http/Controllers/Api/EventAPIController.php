<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Models\Event;
use App\Models\EventJoin;
use App\Models\EventUserMatch;
use App\Models\User;
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

    public function eventUser($id, Request $request, Event $event)
    {
        $event = $event->with('usersInfo')->where('id',$id)->first();
        if($request->user_id){
            $user = User::where('id',$request->user_id)->first();
            // Get all the other users at the event that the current user hasn't already matched with
            $otherUsers = $event->users()->whereNotIn('users.id', function ($query) use ($request, $event) {
                $query->select('matched_id')
                    ->from('event_user_matches')
                    ->where('event_id', $event->id)
                    ->where(function ($q) use ($request) {
                        $q->where('user_id', $request->user_id)
                            ->orWhere('matched_id', $request->user_id);
                    });
            })->where('users.id', '<>', $request->user_id) // Exclude the current user
            ->get();

            // Get the IDs of the users that the current user has already matched with
            $alreadyMatchedIds = $user ? $user->matches()->pluck('matched_id')->toArray() : [];
            // Filter out any users that the current user has already matched with
            $possibleMatches = $otherUsers->reject(function ($otherUser) use ($alreadyMatchedIds) {
                return in_array($otherUser->id, $alreadyMatchedIds);
            });
            // If there are no possible matches left, return an error
            if ($possibleMatches->isEmpty()) {
                return RESTAPIHelper::response(['error' => 'No possible matches'], 404, 'No possible matches.', $this->isBlocked);
            }


//            dd($possibleMatches);
            // Pick a random user to match with
            $matchedUser = $possibleMatches->random();

            // Create the match record
            $match = new EventUserMatch();
            $match->event_id = $event->id;
            $match->user_id = $request->user_id;
            $match->matched_id = $matchedUser->id;
            $match->save();



//            return response()->json([
//                'message' => 'Matched with user successfully',
//                'match' => $match,
//            ]);
            return RESTAPIHelper::response(['match' => $match], 200, 'Matched with user successfully.', $this->isBlocked);
        }

        return RESTAPIHelper::response(['event' => $event], 200, 'Event fetch successfully.', $this->isBlocked);

    }

    public function eventUserJoined(Request $request)
    {
        dd('ss');
    }
}
