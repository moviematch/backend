<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model {

    public $timestamps = false;

    protected $fillable = [
        'word',
        'adjective'
    ];

}
