<?php

use App\Http\Controllers\Api\ArtistsContoller;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserSongsController;
use App\Http\Middleware\Api\AuthenticationMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function() {
    Route::post('login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('api.auth.register');
});

Route::group(['middleware' => AuthenticationMiddleware::class], function() {
    Route::group(['prefix' => 'user'], function() {
        Route::get('/', [AuthController::class, 'information'])->name('api.user.me');
        Route::get('/{client:uuid}', [AuthController::class, 'information'])->name('api.user.other');

        Route::group(['prefix' => '{client:uuid}/playlists'], function() {
            Route::get('/', [UserSongsController::class, 'playlists'])->name('api.user.playlist.all');
            Route::get('/{playlist:uuid}', [UserSongsController::class, 'playlist'])->name('api.user.playlist.specific');
            Route::get('/{playlist:uuid}/songs', [UserSongsController::class, 'songs'])->name('api.user.playlist.songs');

            Route::post('/create', [UserSongsController::class, 'createPlaylist'])->name('api.user.playlist.create');
        });
    });

    Route::group(['prefix' => 'artists'], function() {
        Route::get('/', [ArtistsContoller::class, 'all'])->name('api.artists.all');
        Route::get('/{author:uuid}', [ArtistsContoller::class, 'information'])->name('api.artists.info');
        Route::get('/{author:uuid}/songs/{album:uuid}', [ArtistsContoller::class, 'songs'])->name('api.artists.songs');
    });
});