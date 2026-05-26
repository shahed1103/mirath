<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropDownController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|-------------------------------------------------------- ------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function(){
    Route::post('register' , 'register')
          ->name('user.register');

    Route::post('signin' , 'signin')
    ->name('user.signin');

    Route::post('userForgotPassword' , 'userForgotPassword')
    ->name('user.password.email');

Route::post('userCheckCode' , 'userCheckCode')
->name('user.password.code.check');

Route::post('userResetPassword/{code}' , 'userResetPassword')
->name('user.password.reset');

Route::middleware('auth:sanctum')->get('logout', [AuthController::class, 'logout'])->name('user.logout');
Route::post('authGoogle', [AuthController::class, 'googleSignIn']);
Route::post('setPassword/{email}', [AuthController::class, 'setPassword']);
});

Route::get('getNationalities', [DropDownController::class, 'getNationalities']);
Route::get('getClassifications', [HomeController::class, 'getClassifications']);
Route::middleware('auth:sanctum')->get('getContinueReading', [HomeController::class, 'getContinueReading']);
Route::get('getFeatures', [HomeController::class, 'getFeatures']);

Route::middleware('auth:sanctum')->get('getHome', [HomeController::class, 'getHome']);


Route::middleware('auth:sanctum')->post('updateReadingProgress', [HomeController::class, 'updateReadingProgress']);






