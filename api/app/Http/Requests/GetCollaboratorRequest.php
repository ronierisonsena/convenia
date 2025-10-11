<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="GetCollaboratorRequest",
 *     type="object",
 *     title="Collaborator Request",
 *
 *     @OA\Parameter(
 *          name="name",
 *          in="query",
 *
 *          @OA\Schema(type="string", example="Ted Rubber")
 *     ),
 *
 *     @OA\Parameter(
 *          name="email",
 *          in="query",
 *
 *          @OA\Schema(type="string", example="test@email.com")
 *     ),
 *
 *     @OA\Parameter(
 *          name="cpf",
 *          in="query",
 *
 *          @OA\Schema(type="string", example="111.222.333-45")
 *     ),
 *
 *     @OA\Parameter(
 *          name="city",
 *          in="query",
 *
 *          @OA\Schema(type="string", example="Belo Horizonte")
 *     ),
 *
 *     @OA\Parameter(
 *          name="state",
 *          in="query",
 *
 *          @OA\Schema(type="string", example="Minas Gerais")
 *     ),
 * )
 */
class GetCollaboratorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:80',
            'email' => 'sometimes|string|max:80',
            'cpf' => 'sometimes|string|max:80',
            'city' => 'sometimes|string|max:80',
            'state' => 'sometimes|string|max:80',
        ];
    }
}
