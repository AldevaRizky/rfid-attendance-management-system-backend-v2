<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = [
            ['name' => 'Human Resources', 'description' => 'Responsible for employee welfare and recruitment.', 'head_user_id' => null],
            ['name' => 'Finance', 'description' => 'Handles budgeting, accounting, and financial strategies.', 'head_user_id' => null],
            ['name' => 'Research and Development', 'description' => 'Focuses on innovation and product development.', 'head_user_id' => null],
            ['name' => 'Marketing', 'description' => 'Handles marketing campaigns and branding.', 'head_user_id' => null],
            ['name' => 'Sales', 'description' => 'Responsible for sales and client relationships.', 'head_user_id' => null],
            ['name' => 'IT Support', 'description' => 'Maintains IT systems and infrastructure.', 'head_user_id' => null],
            ['name' => 'Operations', 'description' => 'Ensures smooth daily operations.', 'head_user_id' => null],
            ['name' => 'Customer Service', 'description' => 'Handles customer inquiries and support.', 'head_user_id' => null],
            ['name' => 'Legal', 'description' => 'Manages legal compliance and contracts.', 'head_user_id' => null],
            ['name' => 'Procurement', 'description' => 'Handles purchasing and supplier management.', 'head_user_id' => null],
        ];

        // Loop untuk membuat data divisi
        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
