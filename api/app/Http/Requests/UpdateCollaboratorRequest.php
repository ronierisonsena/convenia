<?php

namespace App\Http\Requests;

use App\Repositories\UserTypeRepository;
use App\Rules\ValidCollaboratorOwner;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateCollaboratorRequest",
 *     type="object",
 *     title="Update Collaborator Request",
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
 *     @OA\Property(
 *          property="type",
 *          type="string",
 *          example="staff"
 *     ),
 * )
 */
class UpdateCollaboratorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $collaborator = $this->route('collaborator');
        $this->merge([
            'collaborator' => $collaborator,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $collaborator */
        $collaborator = $this->route('collaborator');

        return [
            'name' => 'sometimes|string|max:255|min:5',
            'email' => 'sometimes|string|email|max:255|unique:users',
            'password' => 'sometimes|string|min:6|max:150',
            'cpf' => [
                'sometimes',
                'string',
                'regex:/^(\d{3})\.?(\d{3})\.?(\d{3})\-?\d{2}$/',
            ],
            'city' => 'sometimes|string|min:3|max:255',
            'state' => 'sometimes|string|min:2|max:255',
            'collaborator' => new ValidCollaboratorOwner($collaborator),
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
