<?php

namespace App\Services;

use App\Http\Requests\StoreCollaboratorRequest;
use App\Models\Staff;
use App\Models\UserType;
use App\Repositories\StaffRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CsvCollaboratorService extends BaseService
{
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

    /**
     * UserService constructor.
     */
    public function __construct(
        private UserRepository $userRepository,
        private StaffRepository $staffRepository,
    ) {}

    /**
     * Import collaborators from CSV
     */
    public function importCsv(string $filePath, int $managerId)
    {
        if (! file_exists($filePath)) {
            throw new Exception(__('responses.collaborator.csv_not_found'));
        }

        $request = new StoreCollaboratorRequest;
        $collaboratorsToImport = [];
        $collaboratorsNotImported = [];

        $file = fopen($filePath, 'r');

        $columns = fgetcsv($file, 0, ','); // CSV header

        while (($data = fgetcsv($file, 0, ',')) !== false) {
            $rowData = array_combine($columns, $data);

            /** Try associate columns and format collaborator data */
            $collaborator = $this->formatDataToImport($rowData);

            try {
                /** Validate collaborator data before import */
                $validator = Validator($collaborator, $request->rules(), $request->messages());
                $validator->validate();

                $collaboratorsToImport[] = $collaborator;
            } catch (Throwable $e) {
                $collaboratorsNotImported[] = ['name' => $rowData['name'], 'reasons' => $validator->errors()->all()];
            }
        }

        fclose($file);

        DB::beginTransaction();

        /** Insert collaborators */
        $this->userRepository->insert($collaboratorsToImport);

        /** Get all ID's from inserted collaborators */
        $collaboratorsIds = $this->userRepository->getCollaboratorsIdsByEmail(
            collect($collaboratorsToImport)->pluck('email')->toArray()
        );

        $staff = $this->formatStaffManagerId($collaboratorsIds, $managerId);

        /** Insert manager for those staff's */
        $this->staffRepository->insert($staff);

        DB::commit();

        return $collaboratorsNotImported;
    }

    /**
     * Format Staff data
     */
    public function formatStaffManagerId(array $collaboratorsIds, int $managerId): array
    {
        return array_map(function ($id) use ($managerId) {
            return [
                'user_id' => $id,
                'manager_id' => $managerId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $collaboratorsIds);
    }

    /**
     * Format collaborator data to import
     *
     * @return array
     */
    public function formatDataToImport(array $rowData)
    {
        foreach ($rowData as $key => $value) {
            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['name'])) {
                $collaborator['name'] = $value;
            }

            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['email'])) {
                $collaborator['email'] = $value;
            }

            if (in_array($key, self::COLLABORATOR_CSV_POSSIBLE_COLUMNS['cpf'])) {
                $collaborator['cpf'] = $this->onlyNumbers($value);
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
