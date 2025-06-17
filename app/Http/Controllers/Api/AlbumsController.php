<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\AlbumJson;
use App\Models\Album;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;

class AlbumsController extends Controller
{
    public function all() : JsonResponse {
        $date = (new DateTime())->sub(new DateInterval('P1M'));
        return Response::json(AlbumJson::collection(Album::whereDate('created_at', '>', $date)->get()));
    }
}
