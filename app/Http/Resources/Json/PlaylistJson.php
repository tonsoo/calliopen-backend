<?php

namespace App\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Playlist
 */
class PlaylistJson extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'creator' => new BasicClientJson($this->whenLoaded('creator')),
            'cover' => $this->cover->url(),
            'name' => $this->name,
            'total_duration' => $this->total_duration,
            'is_public' => $this->is_public,
            'songs' => SongJson::collection($this->whenLoaded('songs')),
            'collaborators' => BasicClientJson::collection($this->whenLoaded('collaborators')),
        ];
    }
}
