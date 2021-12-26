<?php

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\User\UserController;
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


// AUTH
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [UserAuthController::class, 'register'])->name('register');
    Route::post('login', [UserAuthController::class, 'login'])->name('login');
    Route::post('forgot', [UserAuthController::class, 'forgot'])->name('forgot');
    Route::patch('reset', [UserAuthController::class, 'reset'])->name('reset');
    Route::patch('activate', [UserAuthController::class, 'activate'])->name('activate');
});


Route::group(['middleware' => 'auth:api'], function () {

    // USERS
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index')->middleware('can:users.index');
        Route::get('/{id}', [UserController::class, 'show'])->name('user.show')->middleware('can:users.show');
        Route::post('/', [UserController::class, 'store'])->name('user.store')->middleware('can:users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('user.update')->middleware('can:users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('can:users.destroy');
    });
});