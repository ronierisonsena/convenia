<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidCollaboratorOwner;
use Illuminate\Foundation\Http\FormRequest;

class DestroyCollaboratorRequest extends FormRequest
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
            'collaborator' => new ValidCollaboratorOwner($collaborator),
        ];
    }
}
