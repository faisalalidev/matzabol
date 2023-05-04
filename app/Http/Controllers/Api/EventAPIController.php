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
        $userId = $request->user_id;
        $eventId = $request->event_id;
// Check if the user has already joined the event
        if (EventJoin::where('user_id', $userId)->where('event_id', $eventId)->exists()) {
            return RESTAPIHelper::response(['message' => 'User already joined this event'], 400, 'User already joined this event.', $this->isBlocked);
        }
// If the user has not joined the event yet, create a new record
        $eventJoin = new EventJoin;
        $eventJoin->user_id = $userId;
        $eventJoin->event_id = $eventId;
        $eventJoin->save();
        return RESTAPIHelper::response(['event' => $eventJoin], 200, 'Event Fetch successfully.', $this->isBlocked);
    }

    public function eventUser($id, Request $request, Event $event)
    {
        $event = $event->with('usersInfo')->where('id',$id)->first();
        if($request->user_id){
            $user = User::where('id',$request->user_id)->first();
            // Get all the other users at the event that the current user hasn't already matched with
            $otherUsers = $event->users()->whereNotIn('users.id', function ($query) use ($user, $event) {
                $query->select('matched_id')
                    ->from('event_user_matches')
                    ->where('event_id', $event->id)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhere('matched_id', $user->id);
                    });
            })
            ->whereNotIn('users.id', function ($query) use ($user) {
                $query->select('user_id')
                    ->from('event_user_matches')
                    ->where('matched_id', $user->id);
            })
            ->where('users.id', '!=', $user->id)
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
            $matchedUser = $possibleMatches->random();
            // Create the match record
            $match = new EventUserMatch();
            $match->event_id = $event->id;
            $match->user_id = $request->user_id;
            $match->matched_id = $matchedUser->id;
            $match->save();
            $match->load(['user', 'matchedUser']);
            return RESTAPIHelper::response([
//                'user' => $match->user,
                'matchedUser' => $match->matchedUser,
            ],
                200, 'Matched with user successfully.', $this->isBlocked);
        }

        return RESTAPIHelper::response(['event' => $event], 200, 'Event fetch successfully.', $this->isBlocked);

    }

    public function eventUserJoined(Request $request)
    {
        dd('ss');
    }
}
