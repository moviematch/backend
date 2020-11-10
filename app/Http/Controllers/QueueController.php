<?php

namespace App\Http\Controllers;

use App\Helpers\Id;
use App\Models\Recommendation;
use App\Models\Country;
use App\Models\RoomMember;
use App\Models\Room;
use App\Models\Genre;
use Illuminate\Http\Request;

class QueueController extends Controller {
    
    public function show(){
        $user = RoomMember::Current();
        $room = Room::Current();
        $room->touch();
        // check whether there's any new matches to consider
        foreach($room->getMatches() as $match){
            if($user->recommendations()->where('id', $match->id)->wherePivot('shownMatch', 0)->first()){
                $user->recommendations()->updateExistingPivot($match->id, ['shownMatch' => 1]);
                return [
                    'room' => $room,
                    'match' => $match,
                    'recommendation' => null
                ];
            }
        }
        // pick a new element to recommend; first try and get whatever has been accepted by others in the room
        $recommendations = $room->getFilteredRecommendations(true);
        if($recommendations->count() <= 0){
            // if we can't find any that haven't been accepted by others, find a new entry
            $recommendations = $room->getFilteredRecommendations();
            if($recommendations->count() <= 0){
                // nothing to recommend at the moment :(
                return [
                    'room' => $room,
                    'match' => null,
                    'recommendation' => null
                ];
            }
        }
        $recommendation = $recommendations[0];
        $user->recommendations()->attach($recommendation->id);
        return [
            'room' => $room,
            'match' => null,
            'recommendation' => $recommendation
        ];
    }

    public function update(Request $request){
        $recommendation = Recommendation::find(Id::decode($request->route('recommendation')));
        if(!$recommendation) abort(404, 'Invalid recommendation');
        $user = RoomMember::Current();
        if(!$user) abort(404, 'Invalid token');
        if(!$user->recommendations()->where('id', $recommendation->id)->first()){
            abort(404, 'This item has not been recommended yet');
        }
        $user->room->touch();
        $user->recommendations()->updateExistingPivot($recommendation->id, ['accepted' => 1]);
        return response()->noContent(200);
    }

}
