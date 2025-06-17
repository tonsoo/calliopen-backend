<?php

namespace App\Http\Resources\Json;

use App\Settings\ClientSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Client
 */
class BasicClientJson extends JsonResource
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
            'username' => $this->username,
            'avatar' => $this->avatar?->url() ?? app(ClientSettings::class)->defaultAvatarUrl(),
            'is_artist' => (bool) $this->author
        ];
    }
}
