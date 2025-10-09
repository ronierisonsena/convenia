<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'cpf' => $this->resource->cpf,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'created_at' => $this->resource->created_at->format('Y-m-d H:m:i'),
            'token' => $this->resource->accessToken
        ];
    }
}
