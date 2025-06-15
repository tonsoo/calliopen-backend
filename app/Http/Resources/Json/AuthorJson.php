<?php

namespace App\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Author
 */
class AuthorJson extends JsonResource
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
            'bio' => $this->bio,
            'client' => new BasicClientJson($this->whenLoaded('client')),
            'albums' => AlbumJson::collection($this->whenLoaded('albums')),
            'links' => AuthorLinkJson::collection($this->whenLoaded('links')),
        ];
    }
}
