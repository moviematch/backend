<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\RandomGeneration;
use Illuminate\Support\Str;

class RoomMember extends Model {

    protected $fillable = [
        'name',
        'colour',
        'token',
        'room_id'
    ];

    public function room(){
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function countries(){
        return $this->belongsToMany(Country::class);
    }

    public function recommendations(){
        return $this->belongsToMany(Recommendation::class)->withPivot(['accepted', 'shownMatch']);
    }

    public function toArray(){
        return [
            'name' => $this->name,
            'colour' => $this->colour,
            'me' => $this->token == self::Token(),
            'countries' => $this->countries
        ];
    }

    protected static function Token(){
        return Str::after(request()->header('Authorization', ''), 'Bearer ');
    }

    public static function Current(){
        return self::where('token', self::Token())->first() ?? null;
    }

    public static function Generate($room_id){
        $token = self::Token();
        if(!$token || self::Current() !== null) return null;
        return self::create([
            'name' => RandomGeneration::RandomName(),
            'colour' => RandomGeneration::RandomColour(),
            'token' => $token,
            'room_id' => $room_id
        ]);
    }

}
