<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\AuthorJson;
use App\Http\Resources\Json\SongJson;
use App\Models\Album;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArtistsContoller extends Controller
{
    public function all() : JsonResponse  {
        return Response::json(AuthorJson::collection(Author::all()));
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
}
