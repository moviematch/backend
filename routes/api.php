<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RecommendationFetchController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\QueueController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('announcements', AnnouncementController::class);

Route::get('countries', CountryController::class);
Route::get('countries/{country}/flag', CountryController::class . '@flag');

Route::get('genres', GenreController::class);

Route::get('queue', QueueController::class . '@show');
Route::put('queue/{recommendation}', QueueController::class . '@update');

Route::get('room', RoomController::class . '@show');
Route::post('room', RoomController::class . '@store');
Route::post('room/settings', RoomController::class . '@update');
Route::delete('room', RoomController::class . '@destroy');
Route::get('room/matches', RoomController::class . '@indexMatches');
Route::post('room/join/{code}', RoomController::class . '@join');

Route::patch('cron/recommendations', RecommendationFetchController::class);


Route::fallback(function(){
    return response()->json([
        'message' => 'Unavailable route, please check syntax.'
    ], 404);
});
