<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CastMember extends Model {

    public $with = ['role'];

    public $timestamps = false;

    protected $fillable = [
        'name',
        'role_id'
    ];

    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function recommendations(){
        return $this->belongsToMany(Recommendation::class);
    }

    public function toArray(){
        return [
            'name' => $this->name,
            'role' => $this->role->name
        ];
    }

    public static function Named(string $name, string $roleName){
        $role = Role::Named($roleName);
        $member = self::where('name', $name)->where('role_id', $role->id)->first();
        if(!$member){
            $member = self::create([
                'name' => $name,
                'role_id' => $role->id
            ]);
        }
        return $member;
    }

}
