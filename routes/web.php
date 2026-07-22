<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Broadcast;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
