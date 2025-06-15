<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\AuthorJson;
use App\Http\Resources\Json\SongJson;
use App\Models\Album;
use App\Models\Author;
use App\Models\Client;
use App\Models\File;
use App\Models\Song;
use App\Services\AudioFileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArtistsContoller extends Controller
{
    public function all() : JsonResponse  {
        return Response::json(AuthorJson::collection(Author::all()));
    }

    public function me(Request $request) : JsonResponse {
        $client = $request->user();
        if (!$client->author) {
            return Response::json(['error' => 'You are not an artist.'], 404);
        }

        return Response::json(new AuthorJson($client->author));
    }
    
    public function information(Author $author) : JsonResponse {
        return Response::json(new AuthorJson($author->load(['albums', 'links'])));
    }

    public function songs(Author $author, Album $album) : JsonResponse {
        if ($author->id != $album->creator->id) {
            return Response::json(['error' => 'Arguments mismatch'], 400);
        }

        return Response::json(SongJson::collection($album->songs));
    }

    public function publishSong(Author $author, Request $request) : JsonResponse {
        $data = $request->validate([
            'album' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:255'],
            'cover' => ['required', 'file', 'mimes:jpeg,png,gif,webp', 'max:5000'],
            'lyrics' => ['string', 'max:1000'],
            'track' => ['required', 'file', 'mimes:mp3,wav,ogg,flac,aac,mpeg', 'max:25000'],
            'is_explicit' => ['boolean'],
        ]);

        $album = Album::where('uuid', $data['album'])->first();
        if ($author->client->id !== $request->user()->id || $album?->creator?->id != $author->id) {
            return Response::json(['error' => 'Not author'], 403);
        }

        $service = app(AudioFileService::class);

        try {
            DB::transaction(function() use ($request, $data, $service, $album) {
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
                
                $trackUpload = $request->file('track');

                $convertedPath = Song::UPLOAD_PATH.'converted/';
                $originalPath = $trackUpload->getRealPath();
                $targetName = pathinfo($originalPath, PATHINFO_FILENAME).'.flac';
                $converted = $service->convertToFlac(
                    $originalPath,
                    Storage::disk('public')->path($convertedPath),
                    $targetName,
                );

                $song = new Song([
                    'name' => $data['name'],
                    'album_id' => $album->id,
                    'cover_id' => $coverFile->id,
                    'lyrics' => $data['lyrics'],
                    'is_explicit' => $data['is_explicit'],
                    'view_count' => 0,
                    'file' => $convertedPath.basename($converted),
                    'duration_ms' => $service->durationMs($converted),
                ]);
                $song->save();
            });

            return Response::json();
        } catch (Exception $e) {
            return Response::json(['error' => 'Failed to publish song.'], 500);
        }
    }

    public function removeSong(Author $author, Song $song, Request $request) : JsonResponse {
        if ($author->client->id !== $request->user()->id || $song->album->creator->id !== $author->id) {
            return Response::json(['error' => 'Not authorized'], 403);
        }

        try {
            DB::transaction(function () use ($song) {
                $song->delete();
            });

            return Response::json();
        } catch (Exception $e) {
            return Response::json(['error' => 'Failed to remove song.'], 500);
        }
    }
}
