<?php

namespace Database\Seeders;

use App\Models\UserType;
use App\Repositories\UserRepository;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userRepository = app()->make(UserRepository::class);
        $user = $userRepository->create([
            'id' => 1,
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => '123456',
            'cpf' => '11111111111',
            'city' => 'Belo Horizonte',
            'state' => 'Minas Gerais',
            'user_type_id' => UserType::TYPE_MANAGER,
        ]);

        $user->manager()->create([
            'user_id' => $user->id,
        ]);
    }
}
