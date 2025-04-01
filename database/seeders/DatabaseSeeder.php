<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(EducationSeeder::class);
        $this->call(ShiftSeeder::class);
        $this->call(EmployeeSeeder::class);
    }
}
