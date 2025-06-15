<?php

namespace App\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Song
 */
class SongJson extends JsonResource
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
            'name' => $this->name,
            'album' => new AlbumJson($this->whenLoaded('album')),
            'cover' => $this->cover?->url(),
            'lyrics' => $this->lyrics,
            'is_explicit' => $this->is_explicit,
            'views' => $this->view_count,
            'file' => asset('storage/'.$this->file),
            'duration' => $this->duration_ms,
        ];
    }
}
