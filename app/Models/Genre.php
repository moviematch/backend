<?php

namespace App\Models;

use App\Helpers\Id;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model {

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function recommendations(){
        return $this->belongsToMany(Recommendation::class);
    }

    public function scopeOrderByPopularity($query){
        return $query->withCount('recommendations')->orderBy('recommendations_count', 'desc');
    }

    public function scopePopularOnly($query, int $minCount = 20){
        return $query->has('recommendations', '>=', $minCount);
    }

    public function toArray(){
        return [
            'id' => Id::encode($this->id),
            'name' => $this->name
        ];
    }

    public static function Named(string $name){
        $genre = self::where('name', $name)->first();
        if(!$genre){
            $genre = self::create([
                'name' => $name
            ]);
        }
        return $genre;
    }

}
