<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Helpers\Id;
use App\Helpers\Image;

class CountryController extends Controller
{
    public function __invoke(){
        return Country::withCount('recommendations')->orderBy('name')->get()->where('recommendations_count', '>', 0)->values();
    }

    public function flag(Request $request, string $country){
        // fetch country
        $country = Country::where('id', Id::decode($country))->first();
        if(!$country) abort(404, 'Invalid country ID');
        // get parameters
        $thickness = $request->input('thickness') ?? 2;
        $value = $request->input('value') ?? 170;
        $height = $request->input('height') ?? 40;
        // check whether we already have this flag ready
        $localPath = str_replace('\\', '/', getcwd()) . '/resources/flags/' . $country->code . '-' . $thickness . '-' . $value . '-' . $height . '.png';
        $image = Image::FromFile($localPath);
        if($image !== null){
            return $image->responsePNG();
        }
        // fetch flag image data
        $image = new Image($country->flag);
        // crop contents (ensuring we keep Switzerland's square aspect ratio)
        $image->crop(
            $country->code == 'CH' ? 12 : 2,
            12,
            $country->code == 'CH' ? 40 : 60,
            40
        );
        // scale up or down
        if($height != 40){
            $image->scale($height / 40);
        }
        // add border
        $image->addBorder($thickness, $value);
        // cache result locally
        $image->savePNG($localPath);
        // return image data
        return $image->responsePNG();
    }
}

