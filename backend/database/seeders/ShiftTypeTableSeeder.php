<?php

namespace Database\Seeders;

use App\Models\ShiftType;
use Illuminate\Database\Seeder;

class ShiftTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $shiftType = [
            [
                'name' => 'No Shift',
                'slug' => 'no_shift',

            ],
            [
                'name' => 'Auto Shift',
                'slug' => 'auto_shift',

            ],
            [
                'name' => 'Manual Shift',
                'slug' => 'manual_shift',

            ],

        ];
        ShiftType::insert($shiftType);
    }
}
