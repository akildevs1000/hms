<?php

namespace Database\Seeders;

use App\Models\TaxSlabs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSlabsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxSlabs = [
            [
                'company_id' => 1,
                'start_price' => 0,
                'end_price' => 7499,
                'tax' => 12,
            ],
            [
                'company_id' => 1,
                'start_price' => 7500,
                'end_price' => 9999999,
                'tax' => 18,
            ],

            [
                'company_id' => 2,
                'start_price' => 0,
                'end_price' => 7499,
                'tax' => 12,
            ],
            [
                'company_id' => 2,
                'start_price' => 7500,
                'end_price' => 9999999,
                'tax' => 18,
            ],

            [
                'company_id' => 3,
                'start_price' => 0,
                'end_price' => 7499,
                'tax' => 12,
            ],
            [
                'company_id' => 3,
                'start_price' => 7500,
                'end_price' => 9999999,
                'tax' => 18,
            ],
        ];

        TaxSlabs::insert($taxSlabs);
    }
}
