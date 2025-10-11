<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ManagerResource",
 *     type="object",
 *     title="Staff Manager Resource",
 *
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/UserResource",
 *         description="Collaborator user"
 *     ),
 *     @OA\Property(
 *          property="collaborators",
 *          type="array",
 *
 *          @OA\Items(
 *
 *              @OA\Property(
 *                  property="collaborator",
 *                  type="object",
 *                  ref="#/components/schemas/StaffResource"
 *              )
 *          )
 *     ),
 * )
 */
class ManagerResource extends JsonResource
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
            'user' => UserResource::make($this->resource->user)->setToken($this->newAccessToken),
            'collaborators' => StaffResource::collection($this->resource->staff),
        ];
    }

    public function setToken(?string $token): static
    {
        $this->newAccessToken = $token;

        return $this;
    }
}
