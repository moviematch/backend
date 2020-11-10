<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Settings;
use App\Models\Country;
use App\Models\Role;
use App\Models\CastMember;
use App\Models\Genre;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class RecommendationFetchController extends Controller {

    public function __invoke() {
        // this controller's implementation isn't made available in the public repository, sorry!
		abort(500);
    }

}
