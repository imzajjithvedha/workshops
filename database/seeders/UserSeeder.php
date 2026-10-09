<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::withoutEvents(fn () => User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]));

        Auth::setUser($admin);

        $records = [
            [
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'status' => 'active',
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@example.com',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'status' => 'active',
            ],
        ];

        foreach ($records as $record) {
            User::create($record);
        }
    }
}
