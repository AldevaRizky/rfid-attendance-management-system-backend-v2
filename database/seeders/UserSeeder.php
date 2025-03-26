<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'nip' => '12345678',
            'role' => 'admin',
            'division_id' => null,
            'position_id' => null,
            'education_id' => null,
            'rfid_card_id' => null,
            'phone_number' => '081234567890',
            'gender' => 'Male',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Jakarta',
            'city' => 'Jakarta',
            'address' => 'Jl. Admin No. 1',
            'join_date' => '2020-01-01',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'nip' => '87654321',
            'role' => 'employee',
            'division_id' => null,
            'position_id' => null,
            'education_id' => null,
            'rfid_card_id' => null,
            'phone_number' => '081298765432',
            'gender' => 'Female',
            'birth_date' => '1995-05-15',
            'birth_place' => 'Bandung',
            'city' => 'Bandung',
            'address' => 'Jl. Employee No. 2',
            'join_date' => '2021-06-01',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ]);
    }
}
