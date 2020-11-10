<?php

namespace App\Models;

use App\Helpers\Id;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\RandomGeneration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Room extends Model {

    protected $fillable = [
        'code',
        'showMovies',
        'showSeries'
    ];

    public function users(){
        return $this->hasMany(RoomMember::class);
    }

    public function genres(){
        return $this->belongsToMany(Genre::class);
    }

    public function getMatches(){
        if($this->users->count() < 2) return collect();
        $members = $this->users()->with('countries')->get();
        $matches = collect();
        for($i = 0; $i < $this->users->count(); ++$i){
            $userAccepted = $this->users[$i]->recommendations()
                                            ->wherePivot('accepted', 1)
                                            ->byType($this->showMovies, $this->showSeries)
                                            ->updatedRecently()
                                            ->withGenres($this->genres)
                                            ->inMemberCountries($members)
                                            ->get();
            $matches = $i == 0 ? $userAccepted : $matches->intersect($userAccepted);
        }
        return $matches;
    }

    /** Apply the current filter without any of the recommendations already sent to users and fetches the results */
    public function getFilteredRecommendations(bool $acceptedBySome = false, int $selectCount = 16){
        $members = $this->users()->with('countries')->get();
        return Recommendation::byType($this->showMovies, $this->showSeries)
                                ->updatedRecently()
                                ->withGenres($this->genres)
                                ->inMemberCountries($members)
                                ->notIgnoredBy($members)
                                ->notSentTo(RoomMember::Current())
                                ->acceptedBySomeOf($acceptedBySome ? $members : new Collection)
                                ->orderByRaw('RAND()')
                                ->take($selectCount)
                                ->get()
                                ->sortByDesc('tmdbRating')
                                ->values();
    }

    public function toArray(){
        $this->refresh();
        return [
            'code' => $this->code,
            'users' => $this->users,
            'showMovies' => $this->showMovies == 1,
            'showSeries' => $this->showSeries == 1,
            'genres' => $this->genres
        ];
    }

    public static function settingsParamsValid(array $params){
        return !Validator::make($params, [
            'showMovies' => 'nullable|boolean',
            'showSeries' => 'nullable|boolean',
            'genres' => 'nullable|id_array:Genre',
            'countries' => 'nullable|id_array:Country',
        ])->fails();
    }

    public function updateSettings(array $params, RoomMember $currentUser){
        if(isset($params['showMovies'])) $this->showMovies = $params['showMovies'];
        if(isset($params['showSeries'])) $this->showSeries = $params['showSeries'];
        if(isset($params['genres'])){
            $this->genres()->sync(Id::decodeSeveral($params['genres']));
        }
        if(isset($params['countries'])){
            $currentUser->countries()->sync(Id::decodeSeveral($params['countries']));
        }
        $this->save();
    }

    /**
     * @return Room newly created room with a random room code
     */
    public static function Generate(){
        $length = 4.0;
        do {
            $code = RandomGeneration::RandomString(intval($length), '234679ACDEFGHJKLMNPRTUVWXYZ');
            $length += 0.4; // slowly increase length if necessary
        } while (self::where('code', $code)->first() !== null);
        return self::create([
            'code' => $code
        ]);
    }

    /**
     * @return Room the current room or fails with 404
     */
    public static function Current(){
        $user = RoomMember::Current();
        if(!$user) abort(404, 'Invalid token');
        return $user->room;
    }

    /**
     * Deletes all rooms in the database that haven't been updated for more than 10 hours
     */
    public static function Clean(){
        self::where('updated_at', '<=', Carbon::now()->subMinutes(600)->format('Y-m-d H:i:s'))->delete();
    }

}
