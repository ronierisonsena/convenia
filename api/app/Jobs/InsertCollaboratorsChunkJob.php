<?php

namespace App\Jobs;

use App\Http\Requests\StoreCollaboratorRequest;
use App\Models\UserType;
use App\Services\CsvCollaboratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Throwable;

class InsertCollaboratorsChunkJob implements ShouldQueue
{
    use Queueable;

    private const COLLABORATOR_CSV_POSSIBLE_COLUMNS = [
        'name' => [
            'name',
            'nome',
            'colaborador',
            'collaborator',
        ],
        'email' => [
            'email',
            'mail',
        ],
        'cpf' => [
            'cpf',
            'document',
        ],
        'city' => [
            'city',
            'cidade',
        ],
        'state' => [
            'state',
            'estado',
            'uf',
        ],
    ];

    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(private Collection $chunk, private int $managerId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CsvCollaboratorService $csvCollaboratorService): void
    {
        $collaboratorsToImport = [];

        foreach ($this->chunk as $rowData) {
            /** Try associate columns and format collaborator data */
            $collaborator = $this->formatDataToImport($rowData);

            $this->validateAndUpdateArrayCollaboratorsImportData(
                collaborator: $collaborator,
                rowData: $rowData,
                collaboratorsToImport: $collaboratorsToImport,
                collaboratorsNotImported: $collaboratorsNotImported
            );
        }

        if (empty($collaboratorsToImport) && empty($collaboratorsNotImported)) {
            return;
        }

        $csvCollaboratorService->insertCollaborators($collaboratorsToImport, $this->managerId);
    }

    /**
     * Validate a collaborator data to import
     */
    private function validateAndUpdateArrayCollaboratorsImportData(
        array $collaborator,
        array $rowData,
        &$collaboratorsToImport,
        &$collaboratorsNotImported
    ): void {
        try {
            $collaboratorRequest = new StoreCollaboratorRequest;
            $rules = $collaboratorRequest->rules();

            /** removed email unique to avoid request on each row */
            $rules['email'] = 'required|string|email|max:255';

            /** Validate collaborator data before import */
            $validator = Validator($collaborator, $rules, $collaboratorRequest->messages());
            $validator->validate();

            $collaboratorsToImport[] = $collaborator;
        } catch (Throwable $e) {
            $collaboratorsNotImported[] = [
                'name' => $rowData['name'],
                'reasons' => isset($validator) ? $validator->errors()->all() : $e->getMessage(),
            ];
        }
    }

    /**
     * Format collaborator data to import
     */
    private function formatDataToImport(array $rowData): array
    {
        foreach ($rowData as $key => $value) {
            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['name'])) {
                $collaborator['name'] = $value;
            }

            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['email'])) {
                $collaborator['email'] = $value;
            }

            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['cpf'])) {
                $collaborator['cpf'] = preg_replace('/\D/', '', $value);
            }

            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['city'])) {
                $collaborator['city'] = $value;
            }

            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['state'])) {
                $collaborator['state'] = $value;
            }
        }

        $collaborator['password'] = Hash::make(substr($collaborator['cpf'], 0, 6));
        $collaborator['user_type_id'] = UserType::TYPE_STAFF;
        $collaborator['created_at'] = now();
        $collaborator['updated_at'] = now();

        return $collaborator;
    }
}
