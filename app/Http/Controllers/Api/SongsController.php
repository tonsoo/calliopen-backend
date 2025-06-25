<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\SongJson;
use App\Models\Client;
use App\Models\Song;
use App\Traits\HasPaginations;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SongsController extends Controller
{
    use HasPaginations;

    public function all(Request $request) : JsonResponse {
        $date = (new DateTime())->sub(new DateInterval('P1M'));
        return Response::json(SongJson::collection($this->paginate(Song::with('album')->whereDate('created_at', '>', $date), $request)));
    }

    public function song(Song $song) : JsonResponse {
        return Response::json(new SongJson($song->load('album.creator')));
    }

    public function favoriteSong(Song $song, Request $request) : JsonResponse {
        /** @var Client */ $client = $request->user();
        $state = 'attached';
        if ($client->favoriteSongs()->find($song->id)->isNotEmpty()) {
            $client->favoriteSongs()->detach([$song->id]);
            $state = 'removed';
        } else {
            $client->favoriteSongs()->attach([$song->id]);
        }
        return Response::json([
            'state' => $state,
        ]);
    }
}
