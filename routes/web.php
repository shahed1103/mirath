<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Broadcast;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::get('/test-r2', function () {

    Storage::disk('r2')->put(
        'books/test.txt',
        'Hello from Laravel 12'
    );

    return 'Uploaded Successfully';
});



