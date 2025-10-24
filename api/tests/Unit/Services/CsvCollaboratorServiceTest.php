<?php

namespace Tests\Unit\Services;

use App\Jobs\InsertCollaboratorsChunkJob;
use App\Repositories\StaffRepository;
use App\Repositories\UserRepository;
use App\Services\CsvCollaboratorService;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CsvCollaboratorServiceTest extends TestCase
{
    /** @test */
    public function dispatches_insert_chunk_jobs()
    {
        Queue::fake();

        $userRepo = app(UserRepository::class);
        $staffRepo = app(StaffRepository::class);

        $service = new CsvCollaboratorService($userRepo, $staffRepo);

        $filePath = base_path('tests/Fixtures/test_import.csv');
        if (! file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        file_put_contents($filePath, "name,email,cpf,city,state\nSara Doe,saradoe@email.com,12345678900,São Paulo,SP\nJohn Doe,jondoe@email.com,12345678900,Minas Gerais,MG");

        $service->importCsv($filePath, 1);

        Queue::assertPushed(InsertCollaboratorsChunkJob::class);
    }
}
