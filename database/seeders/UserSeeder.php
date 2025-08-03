<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Anya'],
            ['name' => 'David'],
            ['name' => 'Dina'],
            ['name' => 'Maksim'],
            ['name' => 'Aleksey'],
        ];

        foreach ($users as $user)
        {
            User::create([
                'name'      => $user['name'],
                'api_token' => Str::random(80),
            ]);
        }
    }
}
