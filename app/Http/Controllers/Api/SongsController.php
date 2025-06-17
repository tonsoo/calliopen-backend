<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\SongJson;
use App\Models\Client;
use App\Models\Song;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SongsController extends Controller
{
    public function all() : JsonResponse {
        $date = (new DateTime())->sub(new DateInterval('P1M'));
        return Response::json(SongJson::collection(Song::with('album')->whereDate('created_at', '>', $date)->get()));
    }

    public function song(Song $song) : JsonResponse {
        return Response::json(new SongJson($song->load('album.creator')));
    }

    public function favoriteSong(Song $song) : JsonResponse {
        return Response::json();
    }
}
