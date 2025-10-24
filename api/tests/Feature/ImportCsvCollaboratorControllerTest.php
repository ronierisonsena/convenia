<?php

namespace Tests\Feature;

use App\Jobs\ProcessCsvCollaboratorJob;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ImportCsvCollaboratorControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh --seed');
        Artisan::call('passport:client --personal --name="Default Personal Access Client" --no-interaction --provider=users');
    }

    /** @test */
    public function dispatches_job_to_import_csv()
    {
        Queue::fake();

        $user = User::factory()->create();
        Manager::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->createWithContent(
            'collaborators.csv',
            "name,email,cpf,city,state\nJohn Doe,john@example.com,12345678900,São Paulo,SP"
        );

        Passport::actingAs($user, ['manager']);

        $response = $this->postJson('/api/v1/collaborator/import/csv', [
            'file' => $file,
        ], [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => __('responses.collaborator.importing_csv'),
            ]);

        Queue::assertPushed(ProcessCsvCollaboratorJob::class);
    }

    /** @test */
    public function returns_422_if_file_is_not_csv()
    {
        $user = User::factory()->create();
        Manager::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('collaborators.txt');

        Passport::actingAs($user, ['manager']);

        $response = $this->postJson('/api/v1/collaborator/import/csv', [
            'file' => $file,
        ], [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
