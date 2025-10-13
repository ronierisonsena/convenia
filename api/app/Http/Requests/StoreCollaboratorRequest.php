<?php

namespace App\Http\Requests;

use App\Repositories\UserTypeRepository;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreCollaboratorRequest",
 *     type="object",
 *     title="Store Collaborator Request",
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
 *          property="password",
 *          type="string",
 *          example="MySuperSecretPassword"
 *     ),
 *     @OA\Property(
 *          property="cpf",
 *          type="string",
 *          example="111.222.333-45"
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
 * )
 */
class StoreCollaboratorRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:5',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:150',
            'cpf' => [
                'required',
                'string',
                'regex:/^(\d{3})\.?(\d{3})\.?(\d{3})\-?\d{2}$/',
            ],
            'city' => 'required|string|min:3|max:255',
            'state' => 'required|string|min:2|max:255',
            'type' => [
                'sometimes',
                'exists:user_types,role',
            ],
        ];
    }

    public function messages()
    {
        return [
            'cpf.regex' => 'Invalid CPF. Acceptable formats are: 000.000.000-00 | 00000000000',
            'type.exists' => 'Invalid type. Acceptable types are: '.$this->getAllowedRoles(),
        ];
    }

    /**
     * Return string all roles
     */
    private function getAllowedRoles(): string
    {
        $userTypesRepository = app()->make(UserTypeRepository::class);

        return implode(' | ', $userTypesRepository->model->all()->pluck('role')->toArray());
    }
}
