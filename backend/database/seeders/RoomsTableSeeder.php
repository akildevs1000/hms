<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->truncate();
        DB::table('room_types')->truncate();

        $types = [
            [
                'name' => 'queen',
                'price' => '2800',
                'max_person' => '3',

                'room_only_price'   => '2800',
                'Break_fast_price' => '2900',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price'  => '3100',
                'lunch_with_dinner_price' => '2900',
                'full_board_price' => '3200',

            ],
            [
                'name' => 'king',
                'price' => '3800',
                'max_person' => '4',

                'room_only_price' => '2800',
                'Break_fast_price' => '2900',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price' => '3100',
                'lunch_with_dinner_price'  => '2900',
                'full_board_price' => '3200',
            ],
            [
                'name'       => 'castle',
                'price'      => '2500',
                'max_person' => '2',

                'room_only_price' => '2800',
                'Break_fast_price' => '2900',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price' => '3100',
                'lunch_with_dinner_price' => '2900',
                'full_board_price' => '3200',
            ],
            [
                'name' => 'royal',
                'price' => '6000',
                'max_person' => '3',

                'room_only_price' => '2800',
                'Break_fast_price' => '2900',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price' => '3100',
                'lunch_with_dinner_price' => '2900',
                'full_board_price' => '3200',
            ],
        ];

        $rooms = [

            // queen
            [
                'room_type_id'     => '1',
                'room_no'          => '101',
                'check_in_status'  => '1',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '2',
                'room_no'          => '102',
                'check_in_status'  => '1',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '2',
                'room_no'          => '103',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '3',
                'room_no'          => '104',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '1',
                'room_no'          => '105',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '1',
                'room_no'          => '106',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '203',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '204',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '205',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '206',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '207',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '208',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '303',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '304',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            // 300
            [
                'room_type_id'     => '1',
                'room_no'          => '305',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '306',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '307',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '308',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '403',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '404',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '405',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '406',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '407',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '408',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            // king

            [
                'room_type_id'     => '2',
                'room_no'          => '202',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '2',
                'room_no'          => '302',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '2',
                'room_no'          => '402',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '2',
                'room_no'          => '502',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],


            //castle
            [
                'room_type_id'     => '3',
                'room_no'          => '201',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '3',
                'room_no'          => '301',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '3',
                'room_no'          => '401',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '3',
                'room_no'          => '501',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            // royal
            [
                'room_type_id'     => '4',
                'room_no'          => '503',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '4',
                'room_no'          => '504',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
        ];

        DB::table('rooms')->insert($rooms);
        DB::table('room_types')->insert($types);
    }
}