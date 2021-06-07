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
    Route::apiResource('userDevices', UserDeviceController::class)->missing(function () {
        return error('Device not found');
    });
    Route::apiResource('devices', DeviceController::class)->missing(function () {
        return error('Device not found');
    });
    Route::apiResource('users', UserController::class)->missing(function () {
        return error('User not found');
    });;
    Route::apiResource('places', PlaceController::class)->missing(function () {
        return error('Place not found');
    });
    Route::apiResource('places.userDevices', UserDevicePlaceController::class)->missing(function () {
        return error('Place not found');
    });
});
