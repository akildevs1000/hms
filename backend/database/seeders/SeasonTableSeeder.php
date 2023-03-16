<?php

namespace Database\Seeders;

use App\Models\Weekend;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Weekend::truncate();
        Weekend::create([
            'day' => ["Sun", "Sat"],
            'company_id' => 1
        ]);

        Weekend::create([
            'day' => ["Sun", "Sat"],
            'company_id' => 2
        ]);

        Weekend::create([
            'day' => ["Sun", "Sat"],
            'company_id' => 3
        ]);
    }
}
