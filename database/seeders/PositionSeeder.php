<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat beberapa data posisi
        $positions = [
            ['name' => 'Manager', 'description' => 'Oversees department operations.', 'level' => 1],
            ['name' => 'Team Leader', 'description' => 'Leads a specific team.', 'level' => 2],
            ['name' => 'Senior Developer', 'description' => 'Develops and maintains critical systems.', 'level' => 3],
            ['name' => 'Junior Developer', 'description' => 'Assists in software development tasks.', 'level' => 4],
            ['name' => 'Intern', 'description' => 'Learns and supports team activities.', 'level' => 5],
            ['name' => 'HR Specialist', 'description' => 'Handles HR-specific tasks.', 'level' => 2],
            ['name' => 'Finance Analyst', 'description' => 'Analyzes financial data and reports.', 'level' => 3],
            ['name' => 'Marketing Executive', 'description' => 'Manages marketing campaigns.', 'level' => 4],
            ['name' => 'Sales Representative', 'description' => 'Handles client relationships and sales.', 'level' => 4],
            ['name' => 'Customer Support', 'description' => 'Provides assistance to customers.', 'level' => 5],
        ];

        // Loop untuk membuat data posisi
        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
