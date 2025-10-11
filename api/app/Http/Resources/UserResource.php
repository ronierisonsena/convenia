<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="User",
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
 *          property="token",
 *          type="string",
 *          example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIwMTk5Y2NhMi0yYjExLTcxMzMtODQ2Ny01ZGRhMDNkODVkYmYiLCJqdGkiOiJjNWFlN2RmMWU3MGNkNzdjNmFmNGI5OGZjM2YwN2VjYTAwZWE5NTU3ODEyNWYyYmVlODgwZWFiN2Q4NGU4ZjcyNThiYTY5ZGJmODk4NjhlYyIsImlhdCI6MTc2MDA3NTIyOS4yOTQ5MTcsIm5iZiI6MTc2MDA3NTIyOS4yOTQ5MTgsImV4cCI6MTc2Mjc1MzYyOS4yODkwMTYsInN1YiI6IjIiLCJzY29wZXMiOlsibWFuYWdlciIsInRlc3RlIl19.c4pq08ah6rJTvTTap5DvfWwhT_SQZ0DXh6bBb4qa8KQPY5BI4gvavWvOJq61sRBLGbuF7mXjTVYI7zkFt2ICpxGAdswNmwBAsyO2OY0vfQEhqjwJGqn12JufxnloJs3nSK-5STB1MJdWhMnDKTR3-W0wM1HvRGaNZWI9gy1NGZD4mweDQ9IgiCaiEuYXYUZrSy3Ih9FIS2lrC2qoVexEAQDF06yGAdorLs1ZRr_XnzD7E4XFGy4N-CjdtCM8YYBZg4si_yWgOdLdqHRQQehdZMAl5kxxlV6nAanvc14guOPD5cXDj4IlVFuKp2ZJyotF1tEUJ0Be8AU8IF80uggQpwo7KTvBlUMS-hpdlQNPBEV30AiPpY4UbOiXWPBSbN1uT30NOtoy7tx5FT8qChz3jGDG1-26KPUUQsP9y2CJGl-3hZ4ksqbzKwKKNj4y0Mrmk1x9kqKabYr03MGQkhHMM7IdtqRNJ1HAGMO6WrueQExmx6W0oCiR9OKHx7HHzRkXXxR-JFcgjQPWFgcKT6pbii_x7o2x1O51peFxwmDxYjIVP4gJBUS-LzLvdj4NqWBxALaIHNWMvI2KJCXve4-4BOR8cSa6tb_EhrCfHqxfSn4uYharGTqqVR3g-qnuDiu0JopjcqprRNz_wqmEWWwTGIVwFuZKf-1x9KkEpmMIJiM"
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
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'cpf' => $this->resource->cpf,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'created_at' => $this->resource->created_at->format('Y-m-d H:m:i'),
            'type' => $this->resource->type->role,
            'token' => $this->newAccessToken,
        ];
    }

    public function setToken(?string $token): static
    {
        $this->newAccessToken = $token;

        return $this;
    }
}
