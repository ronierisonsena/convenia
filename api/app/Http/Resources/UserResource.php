<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="User Resource",
 *     required={"name", "email", "password", "cpf", "city", "state"},
 *
 *     @OA\Property(
 *          property="name",
 *          type="string",
 *          example="Ted Rubber"
 *     ),
 *     @OA\Property(
 *          property="email",
 *          type="string",
 *          example="test@email.com"
 *     ),
 *     @OA\Property(
 *          property="cpf",
 *          type="string",
 *          example="11122233345"
 *     ),
 *     @OA\Property(
 *          property="city",
 *          type="string",
 *          example="Belo Horizonte"
 *     ),
 *     @OA\Property(
 *          property="state",
 *          type="string",
 *          example="Minas Gerais"
 *     ),
 *     @OA\Property(
 *          property="created_at",
 *          type="string",
 *          example="2025-10-11 23:10:55"
 *     ),
 *     @OA\Property(
 *          property="type",
 *          type="string",
 *          example="staff"
 *     ),
 *     @OA\Property(
 *          property="token",
 *          type="string",
 *          example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIwMTqnuDiu0JopjcqprRNz_wqmEWWwTGIVwFuZKf-1x9KkEpmMIJiM"
 *     ),
 * )
 */
class UserResource extends JsonResource
{
    private $newAccessToken;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'cpf' => $this->resource->cpf,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'created_at' => $this->resource->created_at->format('Y-m-d H:m:i'),
            'type' => $this->resource->type?->role,
            'token' => $this->newAccessToken,
        ];
    }

    public function setToken(?string $token): static
    {
        $this->newAccessToken = $token;

        return $this;
    }
}
