<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'KTP' => '1111111111111111',
            'nik' => '1111111111111111',
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // Admin
        User::create([
            'KTP' => '2222222222222222',
            'nik' => '2222222222222222',
            'name' => 'Admin',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Teller
        User::create([
            'KTP' => '3333333333333333',
            'nik' => '3333333333333333',
            'name' => 'Teller',
            'email' => 'teller@gmail.com',
            'password' => Hash::make('admin1234'),
            'role' => 'teller',
            'status' => 'active',
        ]);
    }
}
