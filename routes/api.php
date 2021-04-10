<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});



Route::group(['prefix' => 'v1'], function() {
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('me', [ProfileController::class, 'index']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('places', PlaceController::class);
    });
});
