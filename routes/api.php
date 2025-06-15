<?php

use App\Http\Controllers\Api\ArtistsContoller;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserPlaylistsController;
use App\Http\Middleware\Api\AuthenticationMiddleware;
use App\Models\Song;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function() {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
});

Route::group(['middleware' => AuthenticationMiddleware::class], function() {
    Route::group(['prefix' => 'user'], function() {
        Route::get('/', [AuthController::class, 'information'])->name('api.user.me');
        Route::get('/{client:uuid}', [AuthController::class, 'information'])->name('api.user.other');

        Route::group(['prefix' => '{client:uuid}/playlists'], function() {
            Route::get('/', [UserPlaylistsController::class, 'playlists'])->name('api.user.playlist.all');

            Route::group(['prefix' => '{playlist:uuid}'], function() {
                Route::get('/', [UserPlaylistsController::class, 'playlist'])->name('api.user.playlist.specific');
                Route::get('/songs', [UserPlaylistsController::class, 'songs'])->name('api.user.playlist.songs');
                Route::post('/add/{song}', [UserPlaylistsController::class, 'addSong'])->name('api.user.playlist.add');
                Route::post('/remove/{song}', [UserPlaylistsController::class, 'removeSong'])->name('api.user.playlist.remove');
                Route::post('/order', [UserPlaylistsController::class, 'orderSongs'])->name('api.user.playlist.order');
            });

            Route::post('/create', [UserPlaylistsController::class, 'createPlaylist'])->name('api.user.playlist.create');
        });
    });

    Route::group(['prefix' => 'artists'], function() {
        Route::get('/', [ArtistsContoller::class, 'all'])->name('api.artists.all');
        Route::get('/me', [ArtistsContoller::class, 'me'])->name('api.artists.me');

        Route::group(['prefix' => '{author:uuid}'], function() {
            Route::get('/', [ArtistsContoller::class, 'information'])->name('api.artists.info');

            Route::group(['prefix' => 'songs'], function() {
                Route::get('/{album:uuid}', [ArtistsContoller::class, 'songs'])->name('api.artists.songs');
                Route::post('/publish', [ArtistsContoller::class, 'publishSong'])->name('api.artists.pubish');
                Route::post('/{song}/remove', [ArtistsContoller::class, 'removeSong'])->name('api.artists.remove');
            });
        });
    });
});