<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return view('welcome');
});


Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/test-login', function () {

    Auth::loginUsingId(3);

    return redirect('/test-reverb');

});

Route::get('/test-reverb', function(){

    return view('test');

})->middleware('auth');


