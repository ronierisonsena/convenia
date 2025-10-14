<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ImportCsvCollaboratorRequest",
 *     type="object",
 *     title="Import CSV Collaborator Request",
 *     description="Request body for uploading a CSV file containing collaborator data.",
 *
 *     @OA\Property(
 *         property="file",
 *         type="string",
 *         format="binary",
 *         description="The CSV file to upload.",
 *     )
 * )
 */
class ImportCsvCollaboratorRequest extends FormRequest
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
            'file' => 'required|mimes:csv,txt|max:2048',
        ];
    }
}
