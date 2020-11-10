<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Id;

class GenreController extends Controller
{
    public function __invoke(Request $request){
        if(Validator::make($request->all(), [
            'keyword' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'ids' => 'nullable|string|min:1',
        ])->fails()) abort(400, 'Invalid parameters');

        if($request->has('ids')){
            $ids = explode(',', $request->input('ids'));
            $query = Genre::whereIn('id', Id::decodeSeveral($ids));
        }else{
            if($request->has('keyword') && strlen($request->input('keyword')) >= 2){
                $query = Genre::where('name', 'sounds like', $request->input('keyword'))
                            ->orWhere('name', 'like', '%' . $request->input('keyword') . '%');
            }else{
                $query = Genre::orderByPopularity();
            }
            $query = $query->take($request->input('limit') ?? 16)
                            ->skip($request->input('offset') ?? 0)
                            ->popularOnly();
        }
        return $query->get()
                        ->sortBy('name')
                        ->values();
    }
}
