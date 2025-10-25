<?php

namespace Tests\Feature\Jobs;

use App\Jobs\InsertCollaboratorsChunkJob;
use App\Services\CsvCollaboratorService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InsertCollaboratorsChunkJobTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh --seed');
        Artisan::call('passport:client --personal --name="Default Personal Access Client" --no-interaction --provider=users');
    }

    /** @test */
    public function formats_and_inserts_collaborators_correctly()
    {
        $chunk = collect([
            ['name' => 'John Doe', 'email' => 'john@example.com', 'cpf' => '12345678900', 'city' => 'Sao paulo', 'state' => 'SP'],
        ]);

        $csvService = app(CsvCollaboratorService::class);

        (new InsertCollaboratorsChunkJob($chunk, 1))->handle($csvService);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'cpf' => '12345678900',
        ]);
    }

    /** @test */
    public function cant_inserts_collaborators_with_invalid_cpf()
    {
        $chunk = collect([
            ['name' => 'John Doe', 'email' => 'john@example.com', 'cpf' => '1234567890', 'city' => 'Sao paulo', 'state' => 'SP'],
        ]);

        $csvService = app(CsvCollaboratorService::class);

        (new InsertCollaboratorsChunkJob($chunk, 1))->handle($csvService);

        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
            'cpf' => '1234567890',
        ]);
    }
}
