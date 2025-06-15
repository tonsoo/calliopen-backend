<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\PlaylistJson;
use App\Http\Resources\Json\PlaylistSongJson;
use App\Models\Client;
use App\Models\File;
use App\Models\Playlist;
use App\Models\Song;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class UserPlaylistsController extends Controller
{
    private function validatePlaylist(?Client $client, Playlist $playlist) : ?JsonResponse {
        if (!$client) {
            return Response::json(['error' => 'Client not found'], 404);
        }

        if (!$playlist->is_public && $playlist->creator->id != $client->id) {
            return Response::json(['error' => 'Private playlist'], 403);
        }

        return null;
    }

    public function playlists(Client $client, Request $request) : JsonResponse {
        $playlists = $client->id === $request->id
            ? $client->playlists->with(['creator', 'cover', 'collaborators'])
            : $client->playlists->where('is_public')->with(['creator', 'cover', 'collaborators']);
        return Response::json(PlaylistJson::collection($playlists));
    }

    public function playlist(Client $client, Playlist $playlist) : JsonResponse {
        $validation = $this->validatePlaylist($client, $playlist);
        if ($validation != null) return $validation;

        return Response::json(new PlaylistJson($playlist->load(['creator', 'collaborators'])));
    }

    public function songs(Client $client, Playlist $playlist, Request $request) : JsonResponse {
        $validation = $this->validatePlaylist($client, $playlist);
        if ($validation != null) return $validation;

        return Response::json(PlaylistSongJson::collection($playlist->songEntries->load(['song.album.creator', 'addedBy'])));
    }

    public function createPlaylist(Request $request) : JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'is_public' => ['boolean'],
            'cover' => ['file', 'mimes:jpeg,png,gif,webp', 'max:5000'],
        ]);

        try {
            $playlist = null;
            DB::transaction(function() use ($request, $data, &$playlist) {
                $coverUpload = $request->file('cover');

                $mime = $coverUpload->getClientMimeType();
                $name = $coverUpload->getClientOriginalName();
                $size = $coverUpload->getSize();

                $coverFile = new File([
                    'mime' => $mime,
                    'name' => $name,
                    'size' => $size,
                    'file' => $coverUpload->store(File::UPLOAD_PATH),
                ]);
                $coverFile->save();

                $playlist = new Playlist([
                    'name' => $data['name'],
                    'is_public' => $data['is_public'],
                    'cover_id' => $coverFile->id,
                ]);
                $playlist->save();
            });

            if (!$playlist) {
                throw new Exception();
            }

            return Response::json(new PlaylistJson($playlist->load(['creator', 'collaborators'])));
        } catch (Exception $e) {
            return Response::json(['error' => 'Failed to create playlist'], 500);
        }
    }

    public function addSong(Client $client, Playlist $playlist, Song $song, Request $request) : JsonResponse {
        $validation = $this->validatePlaylist($client, $playlist);
        if ($validation != null) return $validation;

        try {
            $playlist->songs()->attach($song->id, [
                'added_by_id' => $request->user()->id,
                'order' => 1,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            return Response::json(['error' => 'Song already in playlist'], 409);
        }

        return Response::json(new PlaylistJson($playlist->load(['creator', 'collaborators'])));
    }

    public function removeSong(Client $client, Playlist $playlist, Song $song, Request $request) : JsonResponse {
        $validation = $this->validatePlaylist($client, $playlist);
        if ($validation != null) return $validation;

        try {
            $deletedCount = $playlist->songs()->detach($song->id);
            if ($deletedCount === 0) {
                return Response::json(['message' => 'Song was not found in the playlist.'], 404);
            }

            $this->reorderSongsAfterRemoval($playlist);

            return Response::json(new PlaylistJson($playlist->load(['creator', 'collaborators'])));
        } catch (UniqueConstraintViolationException $e) {
            return Response::json(['error' => 'Could not remove song from playlist'], 500);
        }
    }

    private function reorderSongsAfterRemoval(Playlist $playlist) {
        $songsInOrder = $playlist->playlistEntries()->orderBy('order')->get();
        DB::transaction(function () use ($songsInOrder) {
            $order = 1;
            foreach ($songsInOrder as $playlistSong) {
                if ($playlistSong->order !== $order) {
                    $playlistSong->order = $order;
                    $playlistSong->saveQuietly();
                }
                $order++;
            }
        });
    }

    public function orderSongs(Client $client, Playlist $playlist, Request $request) : JsonResponse {
        $data = $request->validate([
            'uuids' => 'required|array',
            'uuids.*' => 'required|string|uuid',
        ]);

        $validation = $this->validatePlaylist($client, $playlist);
        if ($validation != null) return $validation;

        $newOrderUuids = $data['uuids'];
        $curUuidMap = $playlist->songs()
            ->pluck('uuid')
            ->map(fn($uuid) => (string)$uuid)
            ->toArray();

        if (count($newOrderUuids) !== count(array_unique($newOrderUuids))) {
            return Response::json(['error' => 'Duplicate uuids provided'], 409);
        }

        $newUuidList = collect($newOrderUuids)->map(fn($uuid) => (string)$uuid);
        $curUuidList = collect($curUuidMap)->map(fn($uuid) => (string)$uuid);

        if ($newUuidList->count() !== $curUuidList->count() ||
            $newUuidList->diff($curUuidList)->isNotEmpty() ||
            $curUuidList->diff($newUuidList)->isNotEmpty())
        {
            return Response::json(['error' => 'Not all songs are present in the new order.'], 422);
        }

        DB::transaction(function () use ($playlist, $newOrderUuids) {
            $order = 1;
            foreach ($newOrderUuids as $uuid) {
                $songToAttach = Song::where('uuid', $uuid)->value('id');
                if (is_null($songToAttach)) {
                    return Response::json(['error' => "Song {$uuid} not found"]);
                }

                $playlist->songs()->updateExistingPivot($songToAttach, ['order' => $order]);

                $order++;
            }
        });

        return Response::json(PlaylistSongJson::collection($playlist->songEntries->load(['song.album.creator', 'addedBy'])));
    }
}
