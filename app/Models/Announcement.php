<?php

namespace App\Models;

use App\Helpers\Id;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model {

    protected $fillable = [
        'title',
        'contents',
        'links',
        'showToNewUsers',
        'showOnce',
        'visible'
    ];

    public function scopeVisible($query, $showInvisible = true){
        if($showInvisible) return $query;
        return $query->where('visible', 1);
    }
    
    public function toArray(){
        return [
            'id' => Id::encode($this->id),
            'title' => $this->title,
            'contents' => $this->contents,
            'links' => json_decode($this->links),
            'showToNewUsers' => $this->showToNewUsers == 1,
            'showOnce' => $this->showOnce == 1
        ];
    }
}
