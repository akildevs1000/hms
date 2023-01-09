<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_modes')->truncate();
        DB::table('id_card_types')->truncate();


        $data = [
            ['id' => 1, 'name' => 'Cash'],
            ['id' => 2, 'name' => 'Card'],
            ['id' => 3, 'name' => 'Online'],
            ['id' => 4, 'name' => 'Bank'],
            ['id' => 5, 'name' => 'UPI'],
            ['id' => 6, 'name' => 'Cheque'],
            ['id' => 7, 'name' => 'City Ledger']
        ];

        $ids = [
            [
                'name' => 'National Identity Card',
            ],
            [
                'name' => 'Aadhar Card',
            ],
            [
                'name' => 'Voter Id Card',
            ],
            [
                'name' => 'Pan Card',
            ],
            [
                'name' => 'Driving License',
            ],
            [
                'name' => 'Passport Number'
            ],
        ];

        DB::table('payment_modes')->insert($data);
        DB::table('id_card_types')->insert($ids);
    }
}
