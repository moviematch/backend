<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public static function Named(string $name){
        $name = strtolower($name);
        $role = self::where('name', $name)->first();
        if(!$role){
            $role = self::create([
                'name' => $name
            ]);
        }
        return $role;
    }

}
