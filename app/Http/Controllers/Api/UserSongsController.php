<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\PlaylistJson;
use App\Http\Resources\Json\PlaylistSongJson;
use App\Models\Client;
use App\Models\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserSongsController extends Controller
{
    public function playlists(Client $client) : JsonResponse {
        return Response::json(PlaylistJson::collection($client->playlists->where('is_public')));
    }

    public function playlist(Client $client, Playlist $playlist) : JsonResponse {
        if (!$playlist->is_public) {
            return Response::json(['error' => 'Private playlist'], 403);
        }

        if ($playlist->creator->id != $client->id) {
            return Response::json(['error' => 'Argument mismatch'], 400);
        }

        return Response::json(new PlaylistJson($playlist->load(['creator', 'collaborators'])));
    }

    public function songs(Client $client, Playlist $playlist, Request $request) : JsonResponse {
        if (!$playlist->is_public) {
            return Response::json(['error' => 'Private playlist'], 403);
        }

        if ($playlist->creator->id != $client->id) {
            return Response::json(['error' => 'Argument mismatch'], 400);
        }

        return Response::json(PlaylistSongJson::collection($playlist->songs->load(['song.album.creator', 'addedBy'])));
    }

    public function createPlaylist(Request $request) : JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'is_public' => ['boolean']
        ]);

        $playlist = new Playlist($data);
        $playlist->save();

        return Response::json(new PlaylistJson($playlist));
    }
}
