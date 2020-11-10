<?php

namespace App\Models;

use App\Helpers\Id;
use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code'
    ];

    public function getFlagAttribute(){
        return 'https://www.countryflags.io/' . $this->code . '/flat/64.png';
    }

    public function getCroppedFlagAttribute(){
        $baseUri = \explode('/api', $_SERVER['REQUEST_URI'])[0];
        return (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . $baseUri . '/api/countries/' . Id::encode($this->id) . '/flag';
    }

    public function recommendations(){
        return $this->belongsToMany(Recommendation::class);
    }

    public function toArray(){
        return [
            'id' => Id::encode($this->id),
            'name' => trim($this->name),
            'code' => $this->code,
            'flag' => $this->croppedFlag
        ];
    }

    public static function Named($name, $code){
        $country = self::where('name', $name)->where('code', $code)->first();
        if(!$country){
            $country = self::create([
                'name' => $name,
                'code' => $code
            ]);
        }
        return $country;
    }

}
