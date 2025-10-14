<?php

namespace App\Http\Resources;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="StaffResource",
 *     type="object",
 *     title="Staff Resource",
 *     description="Resource for staff",
 *
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/UserResource",
 *         description="User from staff"
 *     ),
 *     @OA\Property(
 *         property="manager",
 *         type="object",
 *         description="Staff manager data",
 *         ref="#/components/schemas/ManagerResource"
 *     )
 * )
 */
class StaffResource extends JsonResource
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
            'manager' => $this->resource->manager ? [
                'name' => $this->resource->manager->user->name,
                'email' => $this->resource->manager->user->email,
            ] : null,
        ];
    }

    public function setToken(?string $token): static
    {
        $this->newAccessToken = $token;

        return $this;
    }
}
