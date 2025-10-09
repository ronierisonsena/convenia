<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'password' => 'required|string|min:6',
            'cpf' => [
                'required',
                'string',
                'regex:/^(\d{3})\.?(\d{3})\.?(\d{3})\-?\d{2}$/',
            ],
            'city' => 'required|string|min:3|max:255',
            'state' => 'required|string|min:2|max:255',
        ];
    }

    public function messages()
    {
        return [
            'cpf.regex' => 'Invalid CPF. Acceptable formats are: 000.000.000-00 | 00000000000',
        ];
    }
}
