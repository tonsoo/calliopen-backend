<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json\AlbumJson;
use App\Models\Album;
use App\Traits\HasPaginations;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;

class AlbumsController extends Controller
{
    use HasPaginations;

    public function all(Request $request) : JsonResponse {
        $date = (new DateTime())->sub(new DateInterval('P1M'));
        return Response::json(AlbumJson::collection($this->paginate(Album::whereDate('created_at', '>', $date), $request)));
    }
}
