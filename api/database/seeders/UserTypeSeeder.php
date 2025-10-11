<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_types')->insert([
            [
                'id' => UserType::TYPE_STAFF,
                'name' => 'Staff',
                'role' => 'staff',
                'description' => 'Staff people',
            ],
            [
                'id' => UserType::TYPE_MANAGER,
                'name' => 'Manager',
                'role' => 'manager',
                'description' => 'Staff manager',
            ],
        ]);
    }
}
