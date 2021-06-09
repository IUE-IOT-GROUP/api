<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDeviceController;
use App\Http\Controllers\UserDevicePlaceController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('api.login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('me', [ProfileController::class, 'index']);
    Route::post('devices/data', [DataController::class, 'store']);
    Route::get('devices/data/{device}', [DataController::class, 'show']);

    Route::apiResource('userDevices', UserDeviceController::class)->missing(fn () => error('User device not found'));
    Route::apiResource('devices', DeviceController::class)
        ->except(['destroy'])
        ->missing(fn () => error('Device not found'));
    Route::apiResource('users', UserController::class)->missing(fn () => error('User not found'));
    Route::apiResource('places', PlaceController::class)->missing(fn () => error('Place not found'));
    Route::apiResource('places.userDevices', UserDevicePlaceController::class)->missing(fn () => error('Place not found'));
});
