<?php

namespace App\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AuthorLink
 */
class AuthorLinkJson extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'image' => $this->image->url(),
            'order' => $this->order,
            'is_visible' => $this->is_visible,
        ];
    }
}
