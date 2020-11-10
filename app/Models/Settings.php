<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {

    public $timestamps = false;

    protected $fillable = [
        'recommendationOffset'
    ];

    public static function Instance(){
        $instance = self::first();
        if(!$instance){
            $instance = self::create([
                'recommendationOffset' => 0
            ]);
        }
        return $instance;
    }
}
