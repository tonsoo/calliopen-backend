<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\AlbumJson;
use App\Http\Resources\Json\AuthorJson;
use App\Http\Resources\Json\SongJson;
use App\Models\Album;
use App\Models\Author;
use App\Models\Song;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RecentsController extends Controller
{
    public function albums() : JsonResponse {
        $date = (new DateTime())->sub(new DateInterval('P1M'));
        return Response::json(AlbumJson::collection(Album::whereDate('created_at', '>', $date)->get()));
    }

    public function artists() : JsonResponse {
        $date = (new DateTime())->sub(new DateInterval('P1M'));
        return Response::json(AuthorJson::collection(Author::with('album')->whereDate('created_at', '>', $date)->get()));
    }
}
