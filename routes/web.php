<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-session', function () {
    return response()->json([
        'secure' => request()->isSecure(),
        'csrf' => csrf_token(),
        'session_id' => session()->getId(),
        'session_cookie' => request()->cookie(config('session.cookie')),
        'user' => auth()->user(),
    ]);
});
