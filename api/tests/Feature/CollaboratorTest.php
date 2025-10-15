<?php

namespace Tests\Feature;

use App\Models\Manager;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

        Passport::tokensCan([
            'staff' => 'Staff access',
            'manager' => 'Manager access',
        ]);
    }

    protected function actingAsManager(): User
    {
        $user = User::factory()->create([
            'user_type_id' => UserType::TYPE_MANAGER,
            'password' => Hash::make('secret123'),
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
        $this->actingAsManager();

        User::factory()->count(3)->create([
            'user_type_id' => UserType::TYPE_STAFF,
        ]);

        $response = $this->getJson('/api/v1/collaborators', [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'cpf', 'city', 'state', 'type'],
                ],
                'message'
            ]);
    }

    /** @test */
    public function manager_can_create_collaborator()
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

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user', 'manager'
                ],
                'message'
            ]);
    }

    /** @test */
    public function manager_can_delete_collaborator()
    {
        $user = $this->actingAsManager();

        $collaborator = User::factory()->create([
            'user_type_id' => UserType::TYPE_STAFF,
        ]);

        Staff::factory()->create([
            'user_id' => $collaborator->id,
            'manager_id' => $user->manager->id,
        ]);

        $response = $this->deleteJson("/api/v1/collaborator/{$user->id}", [], [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ])
        && $this->assertNotNull(
            DB::table('users')->where('id', $user->id)->value('deleted_at')
        );

    }

    /** @test */
    public function manager_can_delete_collaborator()
    {
        $this->actingAsManager();

        $collaborator = User::factory()->create([
            'user_type_id' => UserType::TYPE_STAFF,
        ]);

        $response = $this->deleteJson("/api/v1/collaborators/{$collaborator->id}", [], [
            'api-key' => env('API_KEY'),
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $collaborator->id]);
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
