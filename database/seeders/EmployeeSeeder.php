<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Data karyawan yang diberikan
        $employees = [
            [
                'name' => 'Yanto',
                'email' => 'yanto@example.com',
                'password' => Hash::make('password'),
                'nip' => 'EMP001',
                'role' => 'employee',
                'division_id' => 1, // Human Resources
                'position_id' => 2, // Team Leader
                'education_id' => 4, // SMA
                'rfid_card_id' => null,
                'phone_number' => $faker->phoneNumber,
                'gender' => 'Male',
                'birth_date' => $faker->date('Y-m-d', '-20 years'),
                'birth_place' => $faker->city,
                'city' => $faker->city,
                'address' => $faker->address,
                'join_date' => $faker->date('Y-m-d', '-5 years'),
                'status' => 'Active',
            ],
            [
                'name' => 'Iswanoko',
                'email' => 'iswanoko@example.com',
                'password' => Hash::make('password'),
                'nip' => 'EMP002',
                'role' => 'employee',
                'division_id' => 2, // Finance
                'position_id' => 3, // Senior Developer
                'education_id' => 9, // Sarjana
                'rfid_card_id' => null,
                'phone_number' => $faker->phoneNumber,
                'gender' => 'Male',
                'birth_date' => $faker->date('Y-m-d', '-25 years'),
                'birth_place' => $faker->city,
                'city' => $faker->city,
                'address' => $faker->address,
                'join_date' => $faker->date('Y-m-d', '-3 years'),
                'status' => 'Active',
            ],
            [
                'name' => 'Purwanto',
                'email' => 'purwanto@example.com',
                'password' => Hash::make('password'),
                'nip' => 'EMP003',
                'role' => 'employee',
                'division_id' => 3, // R&D
                'position_id' => 4, // Junior Developer
                'education_id' => 8, // Diploma 3
                'rfid_card_id' => null,
                'phone_number' => $faker->phoneNumber,
                'gender' => 'Male',
                'birth_date' => $faker->date('Y-m-d', '-23 years'),
                'birth_place' => $faker->city,
                'city' => $faker->city,
                'address' => $faker->address,
                'join_date' => $faker->date('Y-m-d', '-4 years'),
                'status' => 'Active',
            ],
            [
                'name' => 'Teguh',
                'email' => 'teguh@example.com',
                'password' => Hash::make('password'),
                'nip' => 'EMP004',
                'role' => 'employee',
                'division_id' => 4, // Marketing
                'position_id' => 8, // Marketing Executive
                'education_id' => 5, // SMK
                'rfid_card_id' => null,
                'phone_number' => $faker->phoneNumber,
                'gender' => 'Male',
                'birth_date' => $faker->date('Y-m-d', '-22 years'),
                'birth_place' => $faker->city,
                'city' => $faker->city,
                'address' => $faker->address,
                'join_date' => $faker->date('Y-m-d', '-2 years'),
                'status' => 'Active',
            ],
        ];

        // Menambahkan 6 data karyawan secara acak
        for ($i = 5; $i <= 10; $i++) {
            $employees[] = [
                'name' => $faker->name,
                'email' => "employee$i@example.com",
                'password' => Hash::make('password'),
                'nip' => "EMP00$i",
                'role' => 'employee',
                'division_id' => $faker->numberBetween(1, 10),
                'position_id' => $faker->numberBetween(1, 10),
                'education_id' => $faker->numberBetween(1, 12),
                'rfid_card_id' => null,
                'phone_number' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'birth_date' => $faker->date('Y-m-d', '-20 years'),
                'birth_place' => $faker->city,
                'city' => $faker->city,
                'address' => $faker->address,
                'join_date' => $faker->date('Y-m-d', '-5 years'),
                'status' => $faker->randomElement(['Active', 'Inactive']),
            ];
        }

        // Insert ke database
        User::insert($employees);
    }
}
