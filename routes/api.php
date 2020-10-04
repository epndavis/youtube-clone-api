<?php

use App\Http\Controllers\Api\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/videos')->name('videos')->group(function() {
    Route::get('/', [VideoController::class, 'index'])->name('index');
    Route::post('/', [VideoController::class, 'store'])->name('store');
    Route::get('/{video}', [VideoController::class, 'show'])->name('show');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
