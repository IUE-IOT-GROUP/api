<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/s', function() {
    echo Hash::make('wm785365');
});

Route::get('login', function() {
     abort(500, 'asd');
})->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::get('documentation', [\App\Http\Controllers\SwaggerController::class, 'index'])->name('documentation.index');
Route::get('documentation/api', [\App\Http\Controllers\SwaggerController::class, 'api'])->name('documentation.api');


Route::get('test', [\App\Http\Controllers\TestController::class, 'add_device_data']);
Route::get('test/deviceData/{device}', [\App\Http\Controllers\TestController::class, 'getDeviceData']);
Route::get('devices/data/{device}', [DataController::class, 'show'])->missing(fn() => error('Device not found'));
Route::get('devices/data/{device}/{type}', [DataController::class, 'showParameter'])->missing(fn() => error('Parameter not found'));
