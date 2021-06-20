<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\FogController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlaceFogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceDeviceController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('api.login');

Route::post('devices/data/{user}', [DataController::class, 'store']);

Route::post('users/fog', [UserController::class, 'storeFromFog']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('me', [ProfileController::class, 'index']);
    Route::get('devices/data/{device}', [DataController::class, 'show'])->missing(fn() => error('Device not found'));
    Route::get('devices/data/{device}/{type}', [DataController::class, 'showParameter'])->missing(fn() => error('Parameter not found'));

    Route::apiResource('devices', DeviceController::class)->missing(fn() => error('Device not found'));

    Route::apiResource('fogs', FogController::class)->missing(fn() => error('Fog not found'));

    Route::apiResource('deviceTypes', DeviceTypeController::class)
        ->except(['destroy'])
        ->missing(fn() => error('Device type not found'));

    Route::apiResource('users', UserController::class)->missing(fn() => error('User not found'));
    Route::get('places/fogs', [PlaceController::class, 'fogs'])->name('places.fogs');
    Route::apiResource('places', PlaceController::class)->missing(fn() => error('Place not found'));
    Route::apiResource('places.children', \App\Http\Controllers\PlacePlaceController::class);
    Route::apiResource('places.devices', PlaceDeviceController::class)->missing(fn() => error('Place not found'));
    Route::apiResource('places.fogs', PlaceFogController::class)->missing(fn() => error('Place not found'));
});
