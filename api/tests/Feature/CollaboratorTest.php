<?php

namespace Tests\Feature;

use App\Models\Manager;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh --seed');
        Artisan::call('passport:client --personal --name="Default Personal Access Client" --no-interaction --provider=users');
    }

    protected function actingAsManager(): User
    {
        $user = User::factory()->create([
            'user_type_id' => UserType::TYPE_MANAGER,
            'password' => Hash::make('secret123'),
            'name' => 'Manager Test',
            'email' => 'manager@test.com',
        ]);

        $manager = Manager::factory()->create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user, ['manager']);

        return $user;
    }

    /** @test */
    public function manager_can_list_collaborators()
    {
        $manager = $this->actingAsManager();
        $staffName = 'staff name';
        $staffName2 = 'another staff name';

        $staff1 = User::factory()->create([
            'user_type_id' => UserType::TYPE_STAFF,
            'name' => $staffName
        ]);
        Staff::factory()->create([
            'user_id' => $staff1->id,
            'manager_id' => $manager->id,
        ]);

        $staff2 = User::factory()->create([
            'user_type_id' => UserType::TYPE_STAFF,
            'name' => $staffName2,
        ]);
        Staff::factory()->create([
            'user_id' => $staff2->id,
            'manager_id' => $manager->id,
        ]);

        $response = $this->getJson('/api/v1/collaborators', [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['user', 'manager'],
                ],
                'message',
            ])
            ->assertSee($staffName)
            ->assertSee($staffName2);
    }

    /** @test */
    public function manager_can_create_collaborator()
    {
        $this->actingAsManager();

        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'cpf' => '11122233344',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
        ];

        $response = $this->postJson('/api/v1/collaborator', $payload, [
            'api-key' => env('API_KEY'),
        ]);

        unset($payload['password']);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertSee($payload);

        $this->assertDatabaseHas('users', $payload);
    }

    /** @test */
    public function manager_can_create_collaborator_with_formatted_cpf()
    {
        $this->actingAsManager();

        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'cpf' => '111.222.333-44',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
        ];

        $response = $this->postJson('/api/v1/collaborator', $payload, [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'user', 'manager',
                ],
                'message',
            ]);

        unset($payload['password']);
        $payload['cpf'] = preg_replace('/\D/', '', $payload['cpf']);

        $this->assertDatabaseHas('users', $payload);
    }

    /** @test */
    public function manager_can_update_collaborator()
    {
        $manager = $this->actingAsManager();

        $collaborator = User::factory()->create([
            'email' => 'john.doe@example.com',
        ]);
        Staff::factory()->create([
            'user_id' => $collaborator->id,
            'manager_id' => $manager->id,
        ]);

        $payload = [
            'email' => 'new.doe@example.com',
        ];

        $response = $this->putJson('/api/v1/collaborator/'.$collaborator->id, $payload, [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'email',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('users', $payload);
        $this->assertDatabaseMissing('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    /** @test */
    public function manager_can_delete_collaborator()
    {
        $manager = $this->actingAsManager();

        $collaborator = User::factory()->create([
            'user_type_id' => UserType::TYPE_STAFF,
        ]);

        Staff::factory()->create([
            'user_id' => $collaborator->id,
            'manager_id' => $manager->id,
        ]);

        $response = $this->deleteJson("/api/v1/collaborator/{$collaborator->id}", [], [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertSoftDeleted('users', [
            'id' => $collaborator->id,
        ]);
    }

    /** @test */
    public function manager_cant_delete_collaborator_that_not_belongs_to_him()
    {
        $manager = $this->actingAsManager();

        $anotherManager = User::factory()->create();
        Manager::factory()->create([
            'user_id' => $anotherManager->id,
        ]);
        $collaborator = User::factory()->create([
            'user_type_id' => UserType::TYPE_STAFF,
        ]);

        Staff::factory()->create([
            'user_id' => $collaborator->id,
            'manager_id' => $anotherManager->id,
        ]);

        $response = $this->deleteJson("/api/v1/collaborator/{$collaborator->id}", [], [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function cannot_access_without_api_key()
    {
        $this->actingAsManager();

        $response = $this->getJson('/api/v1/collaborators');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized.']);
    }

    /** @test */
    public function cannot_access_without_bearer_token()
    {
        $response = $this->getJson('/api/v1/collaborators', [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(401);
    }
}
