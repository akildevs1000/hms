<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FoodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('food_prices')->truncate();

        $foodForThanjavur = [
            // room type for thanjavur
            [
                'type' => 'adult',
                'breakfast' => '200',
                'lunch' => '450',
                'dinner' => '400',
                'company_id' => 1,
            ],
            [
                'type' => 'child',
                'breakfast' => '100',
                'lunch' => '250',
                'dinner' => '250',
                'company_id' => 1,
            ],
            [
                'type' => 'baby',
                'breakfast' => '0',
                'lunch' => '0',
                'dinner' => '0',
                'company_id' => 1,
            ],
        ];


        DB::table('food_prices')->insert($foodForThanjavur);

        // ==========================

        $foodForKodai = [
            [
                'type' => 'adult',
                'breakfast' => '250',
                'lunch' => '500',
                'dinner' => '500',
                'company_id' => 2,
            ],
            [
                'type' => 'child',
                'breakfast' => '150',
                'lunch' => '300',
                'dinner' => '300',
                'company_id' => 2,
            ],
            [
                'type' => 'baby',
                'breakfast' => '0',
                'lunch' => '0',
                'dinner' => '0',
                'company_id' => 2,
            ],
        ];


        DB::table('food_prices')->insert($foodForKodai);
    }
}
