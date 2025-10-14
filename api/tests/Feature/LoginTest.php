<?php

namespace Tests\Feature;

use App\Models\Manager;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LoginTest extends TestCase
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

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        config(['auth.defaults.guard' => 'web']);

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('secret123'),
            'user_type_id' => UserType::TYPE_MANAGER,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ], [
            'api-key' => env('API_KEY', 'fake-api-key'),
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'cpf',
                'city',
                'state',
                'created_at',
                'type',
                'token',
            ],
            'message'
        ]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ], [
            'api-key' => env('API_KEY', 'fake-api-key'),
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Invalid user or password',
        ]);
    }

    /** @test */
    public function user_cannot_login_without_api_key()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized.']);
    }

    /** @test */
    public function authenticated_user_can_access_me_endpoint()
    {

        Passport::tokensCan([
            'staff' => 'Staff access',
            'manager' => 'Manager access',
        ]);

        $user = User::factory()->create([
            'user_type_id' => UserType::TYPE_MANAGER,
        ]);

        $manager = Manager::create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user, ['manager']);

        $response = $this->getJson('/api/v1/me', [
            'api-key' => env('API_KEY', 'fake-api-key'),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'message'
        ]);
    }
}
