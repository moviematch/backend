<?php

namespace App\Models;

use App\Helpers\Id;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recommendation extends Model {
    
    protected $fillable = [
        'title',
        'releaseYear',
        'description',
        'type',
        'type_id',
        'runtime',
        'tmdbRating',
    //    'rottenTomatoesRating',
        'cover',
        'netflix_id'
    ];

    private const types = [
        'movie',
        'series'
    ];

    public function getTypeAttribute(){
        return self::types[$this->attributes['type_id'] ?? 0];
    }

    public function setTypeAttribute($value){
        $type_id = array_search($value, self::types);
        if($type_id === false){
            abort(500, "Illegal recommendation type '$value'; must be 'movie' or 'series'");
        }
        $this->attributes['type_id'] = $type_id;
    }

    public function genres(){
        return $this->belongsToMany(Genre::class);
    }

    public function cast(){
        return $this->belongsToMany(CastMember::class);
    }

    public function countries(){
        return $this->belongsToMany(Country::class);
    }

    public function members(){
        return $this->belongsToMany(RoomMember::class)->withPivot(['accepted', 'shownMatch']);
    }

    /**
     * Builds the query to show either only movies, only series, or both
     */
    public function scopeByType($query, bool $allowMovies, bool $allowSeries){
        if($allowMovies && $allowSeries) return $query;
        if($allowMovies) return $query->where('type_id', 0);
        if($allowSeries) return $query->where('type_id', 1);
        return $query;
    }

    /**
     * Builds the query to show only resources that have been updated recently enough
     */
    public function scopeUpdatedRecently($query, int $days = 7){
        return $query->where('updated_at', '>=', Carbon::now()->subDays($days)->format('Y-m-d H:i:s'));
    }

    /**
     * Builds the query to select recommendations with at least one of the specified genres
     */
    public function scopeWithGenres($query, $genres){
        if($genres->count() <= 0) return $query;// default on all genres
        $genres = $genres->map(function($genre) { return $genre->id; });
        return $query->whereHas('genres', function($query) use ($genres){
            return $query->whereIn('id', $genres);
        });
    }

    /**
     * Builds the query to select recommendations with at least one of the specified countries
     */
    public function scopeInCountries($query, $countries){
        if($countries->count() <= 0) return $query;// default on no country restrictions
        $countries = $countries->map(function($country) { return $country->id; });
        return $query->whereHas('countries', function($query) use($countries){
            return $query->whereIn('id', $countries);
        });
    }

    /**
     * Builds the query to select recommendations with at least one country per room member given
     */
    public function scopeInMemberCountries($query, $members){
        foreach($members as $member){
            $query = $query->inCountries($member->countries);
        }
        return $query;
    }

    /**
     * Builds the query to filter out any recommendations that have been shown to and not accepted by some users
     */
    public function scopeNotIgnoredBy($query, $members){
        //return $query;
        if($members->count() <= 0) return $query;// default on no restrictions
        $members = $members->map(function($member) { return $member->id; });
        return $query->whereDoesntHave('members', function($query) use ($members){
            return $query->whereIn('id', $members)->where('accepted', 0);
        });
    }

    /**
     * Builds the query to select recommendations that have been accepted by at least some of the users
     */
    public function scopeAcceptedBySomeOf($query, $members){
        //return $query;
        if($members->count() <= 0) return $query;
        $members = $members->map(function($member) { return $member->id; });
        return $query->whereHas('members', function($query) use ($members){
            return $query->whereIn('id', $members)->where('accepted', 1);
        });
    }

    /**
     * Builds the query to filter out any recommendations that have already been shown to the user
     */
    public function scopeNotSentTo($query, $member){
        return $query->whereDoesntHave('members', function($query) use ($member){
            return $query->where('id', $member->id);
        });
    }

    public function toArray(){
        return [
            'id' => Id::encode($this->id),
            'title' => utf8_decode($this->title),
            'releaseYear' => $this->releaseYear,
            'description' => $this->description ?? '',
            'type' => $this->type,
            'runtime' => $this->runtime,
            'genres' => $this->genres()->popularOnly()->get(),
            'countries' => $this->countries->sortBy('name')->values(),
            'cast' => $this->cast,
            'tmdbRating' => $this->tmdbRating,
        //    'rottenTomatoesRating' => $this->rottenTomatoesRating,
            'cover' => $this->cover,
            'netflixLink' => $this->netflix_id ? 'https://www.netflix.com/watch/' . $this->netflix_id : null
        ];
    }

}
