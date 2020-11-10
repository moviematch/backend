<?php

namespace App\Http\Controllers;

use App\Helpers\RandomGeneration;
use App\Models\RoomMember;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Helpers\Id;

class RoomController extends Controller {
    
    public function show(){
        $user = RoomMember::Current();
        if(!$user) abort(404, 'Invalid user token');
        $user->room->touch();
        return $user->room;
    }

    public function store(Request $request){
        if(!Room::settingsParamsValid($request->all())) abort(400, 'Invalid input');
        $room = Room::Generate();
        $user = RoomMember::Generate($room->id);
        if(!$user){
            $room->delete();
            abort(400, 'Invalid user token');
        }
        $room->updateSettings($request->all(), $user);
        return $room;
    }

    public function update(Request $request){
        if(!Room::settingsParamsValid($request->all())) abort(400, 'Invalid input');
        Room::Current()->updateSettings($request->all(), RoomMember::Current());
        return response()->noContent(200);
    }

    public function destroy(){
        $user = RoomMember::Current();
        $room = Room::Current();
        if($room->users()->count() <= 1){
            $room->delete();
        }else{
            $room->touch();
            $user->delete();
        }
        return response()->noContent(200);
    }

    public function indexMatches(){
        $room = Room::Current();
        $room->touch();
        return $room->getMatches();
    }

    public function join(Request $request){
        $code = $request->route('code');
        $room = Room::where('code', $code)->first();
        if(!$room) abort(404, 'Invalid room code');
        $user = RoomMember::Generate($room->id);
        if(!$user) abort(400, 'Invalid token or user');
        $room->touch();
        if(count($request->json()->all())){
            $user->countries()->sync(Id::decodeSeveral($request->json()->all()));
        }
        return $room;
    }

}
