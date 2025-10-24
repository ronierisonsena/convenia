<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessCsvCollaboratorJob;
use App\Mail\JobNotificationMail;
use App\Models\Manager;
use App\Models\User;
use App\Services\CsvCollaboratorService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessCsvCollaboratorJobTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh --seed');
        Artisan::call('passport:client --personal --name="Default Personal Access Client" --no-interaction --provider=users');
    }

    /** @test */
    public function processes_csv_and_sends_email()
    {
        Mail::fake();
        Storage::fake('local');

        $user = User::factory()->create();
        Manager::factory()->create(['user_id' => $user->id]);

        $csvPath = 'collaborator/csv/test.csv';
        Storage::put($csvPath, "name,email,cpf,city,state\nJane,jane@example.com,12345678900,Rio, RJ");

        $mockService = $this->createMock(CsvCollaboratorService::class);
        $mockService->expects($this->once())
            ->method('importCsv')
            ->willReturn([]);

        app()->instance(CsvCollaboratorService::class, $mockService);

        (new ProcessCsvCollaboratorJob($csvPath, $user))->handle($mockService);

        Mail::assertSent(JobNotificationMail::class);
        Storage::assertMissing($csvPath);
    }

    /** @test */
    public function processes_csv_and_sends_email_with_collaborators_not_imported()
    {
        Mail::fake();
        Storage::fake('local');

        $user = User::factory()->create();
        Manager::factory()->create(['user_id' => $user->id]);

        $csvPath = 'collaborator/csv/test.csv';
        Storage::put($csvPath, "name,email,cpf,city,state\nJane,jane@example.com,12345678900,Rio, RJ\nRobb,robb@example.com,1234567890,Rio, RJ");

        $mockService = $this->createMock(CsvCollaboratorService::class);
        $mockService->expects($this->once())
            ->method('importCsv')
            ->willReturn(['Robb' => 'Invalid CPF. Acceptable formats are: 000.000.000-00 | 00000000000']);

        app()->instance(CsvCollaboratorService::class, $mockService);

        (new ProcessCsvCollaboratorJob($csvPath, $user))->handle($mockService);

        Mail::assertSent(JobNotificationMail::class);
        Storage::assertMissing($csvPath);
    }
}
