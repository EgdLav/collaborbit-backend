<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'time' => $this->created_at->format('H:i'),
            'date' => $this->created_at->format('Y-m-d'),
            'user' => new UserResource(
                $this->whenLoaded('user')
            ),
            'is_mine' => auth()->id() === $this->user_id,
        ];
    }
}
