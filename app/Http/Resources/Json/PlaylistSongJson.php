<?php

namespace App\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PlaylistSong
 */
class PlaylistSongJson extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'playlist' => new PlaylistJson($this->whenLoaded('playlist')),
            'song' => new SongJson($this->whenLoaded('song')),
            'added_by' => new BasicClientJson($this->whenLoaded('addedBy')),
            'order' => $this->order,
            'added_at' => $this->created_at,
        ];
    }
}
