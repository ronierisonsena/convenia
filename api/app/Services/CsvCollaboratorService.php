<?php

namespace App\Services;

use App\Jobs\InsertCollaboratorsChunkJob;
use App\Repositories\StaffRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CsvCollaboratorService extends BaseService
{
    private const CHUNK_SIZE = 200;

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
    public function importCsv(string $filePath, int $managerId): array
    {
        if (! file_exists($filePath)) {
            throw new Exception(__('responses.collaborator.csv_not_found'));
        }

        $csvRows = $this->getCollectRowsFromCsvFile($filePath);
        $collaboratorsNotImported = [];

        $csvRows->chunk(self::CHUNK_SIZE)->each(function (Collection $chunk) use (
            $managerId,
            &$collaboratorsNotImported
        ) {
            InsertCollaboratorsChunkJob::dispatch($chunk, $managerId);
        });

        return $collaboratorsNotImported;
    }

    /**
     * Insert Collaborators
     */
    public function insertCollaborators(array $collaboratorsToImport, int $managerId): void
    {
        [$userRepository, $staffRepository] = [$this->userRepository, $this->staffRepository];

        DB::transaction(function () use ($collaboratorsToImport, $managerId, $userRepository, $staffRepository) {
            /** Insert collaborators */
            $userRepository->insert($collaboratorsToImport);

            /** Get all ID's from inserted collaborators */
            $collaboratorsIds = $userRepository->getCollaboratorsIdsByEmail(
                collect($collaboratorsToImport)->pluck('email')->toArray()
            );

            $staff = $this->formatStaffData($collaboratorsIds, $managerId);

            /** Insert manager for those staff's */
            $staffRepository->insert($staff);
        });
    }

    /**
     * Format Staff data
     */
    private function formatStaffData(array $collaboratorsIds, int $managerId): array
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

    private function getCollectRowsFromCsvFile(string $filePath): Collection
    {
        $rows = collect();

        $file = fopen($filePath, 'r');

        $columns = fgetcsv($file, 0, ','); // CSV header

        while (($data = fgetcsv($file, 0, ',')) !== false) {
            $rows->push(array_combine($columns, $data));
        }

        fclose($file);

        return $rows;
    }
}
