<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller {
    public function __invoke(Request $request){
        return Announcement::visible($request->boolean('showInvisible'))->get();
    }
}
