<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat 3 data shift
        $shifts = [
            [
                'name' => 'Shift Pagi',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'grace_period' => 15, // Dalam menit
                'max_late_time' => 30, // Dalam menit
            ],
            [
                'name' => 'Shift Siang',
                'start_time' => '16:00:00',
                'end_time' => '00:00:00',
                'grace_period' => 15, // Dalam menit
                'max_late_time' => 30, // Dalam menit
            ],
            [
                'name' => 'Shift Malam',
                'start_time' => '00:00:00',
                'end_time' => '08:00:00',
                'grace_period' => 15, // Dalam menit
                'max_late_time' => 30, // Dalam menit
            ],
        ];

        // Loop untuk membuat data shift
        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
