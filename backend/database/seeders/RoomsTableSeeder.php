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

        $typesForThanjavur = [
            // room type for thanjavur
            [
                'name' => 'queen',
                'price' => '3000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '3000',
                'Break_fast_price' => '3000',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price'  => '3000',
                'lunch_with_dinner_price' => '3000',
                'full_board_price' => '3000',
                'company_id' => 1
            ],
            [
                'name' => 'king',
                'price' => '3400',
                'adult' => '2',
                'child' => '1',
                'baby' => '1',
                'max_person' => '4',
                'room_only_price'   => '3400',
                'Break_fast_price' => '3400',
                'Break_fast_with_dinner_price' => '3400',
                'Break_fast_with_lunch_price'  => '3400',
                'lunch_with_dinner_price' => '3400',
                'full_board_price' => '3400',
                'company_id' => 1
            ],
            [
                'name'       => 'castle',
                'price' => '3000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '3000',
                'Break_fast_price' => '3000',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price'  => '3000',
                'lunch_with_dinner_price' => '3000',
                'full_board_price' => '3000',
                'company_id' => 1
            ],
            [
                'name' => 'suite',
                'price' => '4500',
                'adult' => '3',
                'child' => '2',
                'baby' => '1',
                'max_person' => '6',
                'room_only_price'   => '4500',
                'Break_fast_price' => '4500',
                'Break_fast_with_dinner_price' => '4500',
                'Break_fast_with_lunch_price'  => '4500',
                'lunch_with_dinner_price' => '4500',
                'full_board_price' => '4500',
                'company_id' => 1
            ],
            [
                'name' => 'palace',
                'price' => '6500',
                'adult' => '4',
                'child' => '2',
                'baby' => '1',
                'max_person' => '7',
                'room_only_price'   => '6500',
                'Break_fast_price' => '6500',
                'Break_fast_with_dinner_price' => '6500',
                'Break_fast_with_lunch_price'  => '6500',
                'lunch_with_dinner_price' => '6500',
                'full_board_price' => '6500',
                'company_id' => 1
            ],
        ];

        $roomsForThanjavur = [

            // room for thanjavur
            // queen
            [
                'room_type_id'     => '1',
                'room_no'          => '103',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '2',
                'room_no'          => '104',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '2',
                'room_no'          => '105',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '3',
                'room_no'          => '106',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '1',
                'room_no'          => '105',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '1',
                'room_no'          => '106',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '203',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '204',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '205',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '206',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],



            [
                'room_type_id'     => '1',
                'room_no'          => '303',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '304',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            // 300
            [
                'room_type_id'     => '1',
                'room_no'          => '305',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '306',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '307',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '308',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '403',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '404',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '405',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '406',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '407',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '1',
                'room_no'          => '408',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            // king

            [
                'room_type_id'     => '2',
                'room_no'          => '201',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '2',
                'room_no'          => '301',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '2',
                'room_no'          => '401',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '2',
                'room_no'          => '501',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],


            //castle
            [
                'room_type_id'     => '3',
                'room_no'          => '107',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '3',
                'room_no'          => '108',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            [
                'room_type_id'     => '3',
                'room_no'          => '207',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '3',
                'room_no'          => '208',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],

            // suite
            [
                'room_type_id'     => '4',
                'room_no'          => '202',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '4',
                'room_no'          => '302',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '4',
                'room_no'          => '402',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '4',
                'room_no'          => '502',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            //palace
            [
                'room_type_id'     => '5',
                'room_no'          => '503',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
            [
                'room_type_id'     => '5',
                'room_no'          => '504',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 1
            ],
        ];

        DB::table('room_types')->insert($typesForThanjavur);
        DB::table('rooms')->insert($roomsForThanjavur);


        // ==========================

        $typesForKodai = [
            // room type for thanjavur
            [
                'name' => 'queen',
                'price' => '2800',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '2800',
                'Break_fast_price' => '2800',
                'Break_fast_with_dinner_price' => '2800',
                'Break_fast_with_lunch_price'  => '2800',
                'lunch_with_dinner_price' => '2800',
                'full_board_price' => '2800',
                'company_id' => 2
            ],
            [
                'name' => 'castle',
                'price' => '3800',
                'adult' => '3',
                'child' => '0',
                'baby' => '1',
                'max_person' => '4',
                'room_only_price'   => '3800',
                'Break_fast_price' => '3800',
                'Break_fast_with_dinner_price' => '3800',
                'Break_fast_with_lunch_price'  => '3800',
                'lunch_with_dinner_price' => '3800',
                'full_board_price' => '3800',
                'company_id' => 2
            ],
            [
                'name' => 'king',
                'price' => '3200',
                'adult' => '2',
                'child' => '1',
                'baby' => '1',
                'max_person' => '4',
                'room_only_price'   => '3200',
                'Break_fast_price' => '3200',
                'Break_fast_with_dinner_price' => '3200',
                'Break_fast_with_lunch_price'  => '3200',
                'lunch_with_dinner_price' => '3200',
                'full_board_price' => '3200',
                'company_id' => 2
            ],
            [
                'name' => 'palace',
                'price' => '5000',
                'adult' => '4',
                'child' => '2',
                'baby' => '2',
                'max_person' => '8',
                'room_only_price'   => '5000',
                'Break_fast_price' => '5000',
                'Break_fast_with_dinner_price' => '5000',
                'Break_fast_with_lunch_price'  => '5000',
                'lunch_with_dinner_price' => '5000',
                'full_board_price' => '5000',
                'company_id' => 2
            ],
            [
                'name' => 'honey',
                'price' => '4500',
                'adult' => '2',
                'child' => '0',
                'baby' => '0',
                'max_person' => '2',
                'room_only_price'   => '4500',
                'Break_fast_price' => '4500',
                'Break_fast_with_dinner_price' => '4500',
                'Break_fast_with_lunch_price'  => '4500',
                'lunch_with_dinner_price' => '4500',
                'full_board_price' => '4500',
                'company_id' => 2
            ],
            [
                'name' => 'premium',
                'price' => '3000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '3000',
                'Break_fast_price' => '3000',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price'  => '3000',
                'lunch_with_dinner_price' => '3000',
                'full_board_price' => '3000',
                'company_id' => 2
            ],
            [
                'name' => 'grand',
                'price' => '4000',
                'adult' => '3',
                'child' => '0',
                'baby' => '0',
                'max_person' => '3',
                'room_only_price'   => '4000',
                'Break_fast_price' => '4000',
                'Break_fast_with_dinner_price' => '4000',
                'Break_fast_with_lunch_price'  => '4000',
                'lunch_with_dinner_price' => '4000',
                'full_board_price' => '4000',
                'company_id' => 2
            ],


        ];

        $roomsForKodai = [
            // room for thanjavur
            // queen
            [
                'room_type_id'     => '6',
                'room_no'          => '102',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '104',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '105',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '107',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '108',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '109',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '202',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '204',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '206',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '207',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '209',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '210',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '211',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            // castle

            [
                'room_type_id'     => '7',
                'room_no'          => '101',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '7',
                'room_no'          => '103',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '7',
                'room_no'          => '203',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '7',
                'room_no'          => '205',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],


            //king
            [
                'room_type_id'     => '8',
                'room_no'          => '303',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '305',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '308',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '309',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '310',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],


            // palace
            [
                'room_type_id'     => '9',
                'room_no'          => '106',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '9',
                'room_no'          => '208',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            //honey
            [
                'room_type_id'     => '10',
                'room_no'          => '201',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '10',
                'room_no'          => '301',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '10',
                'room_no'          => '311',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '10',
                'room_no'          => '312',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            //premium
            [
                'room_type_id'     => '11',
                'room_no'          => '304',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '11',
                'room_no'          => '306',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],

            //grand
            [
                'room_type_id'     => '12',
                'room_no'          => '302',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
            [
                'room_type_id'     => '12',
                'room_no'          => '307',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 2
            ],
        ];

        DB::table('room_types')->insert($typesForKodai);
        DB::table('rooms')->insert($roomsForKodai);

        // demo

        $typesForDemo = [
            // room type for thanjavur
            [
                'name' => 'queen',
                'price' => '2800',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '2800',
                'Break_fast_price' => '2800',
                'Break_fast_with_dinner_price' => '2800',
                'Break_fast_with_lunch_price'  => '2800',
                'lunch_with_dinner_price' => '2800',
                'full_board_price' => '2800',
                'company_id' => 3
            ],
            [
                'name' => 'castle',
                'price' => '3800',
                'adult' => '3',
                'child' => '0',
                'baby' => '1',
                'max_person' => '4',
                'room_only_price'   => '3800',
                'Break_fast_price' => '3800',
                'Break_fast_with_dinner_price' => '3800',
                'Break_fast_with_lunch_price'  => '3800',
                'lunch_with_dinner_price' => '3800',
                'full_board_price' => '3800',
                'company_id' => 3
            ],
            [
                'name' => 'king',
                'price' => '3200',
                'adult' => '2',
                'child' => '1',
                'baby' => '1',
                'max_person' => '4',
                'room_only_price'   => '3200',
                'Break_fast_price' => '3200',
                'Break_fast_with_dinner_price' => '3200',
                'Break_fast_with_lunch_price'  => '3200',
                'lunch_with_dinner_price' => '3200',
                'full_board_price' => '3200',
                'company_id' => 3
            ],
            [
                'name' => 'palace',
                'price' => '5000',
                'adult' => '4',
                'child' => '2',
                'baby' => '2',
                'max_person' => '8',
                'room_only_price'   => '5000',
                'Break_fast_price' => '5000',
                'Break_fast_with_dinner_price' => '5000',
                'Break_fast_with_lunch_price'  => '5000',
                'lunch_with_dinner_price' => '5000',
                'full_board_price' => '5000',
                'company_id' => 3
            ],
            [
                'name' => 'honey',
                'price' => '4500',
                'adult' => '2',
                'child' => '0',
                'baby' => '0',
                'max_person' => '2',
                'room_only_price'   => '4500',
                'Break_fast_price' => '4500',
                'Break_fast_with_dinner_price' => '4500',
                'Break_fast_with_lunch_price'  => '4500',
                'lunch_with_dinner_price' => '4500',
                'full_board_price' => '4500',
                'company_id' => 3
            ],
            [
                'name' => 'premium',
                'price' => '3000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '3000',
                'Break_fast_price' => '3000',
                'Break_fast_with_dinner_price' => '3000',
                'Break_fast_with_lunch_price'  => '3000',
                'lunch_with_dinner_price' => '3000',
                'full_board_price' => '3000',
                'company_id' => 3
            ],
            [
                'name' => 'grand',
                'price' => '4000',
                'adult' => '3',
                'child' => '0',
                'baby' => '0',
                'max_person' => '3',
                'room_only_price'   => '4000',
                'Break_fast_price' => '4000',
                'Break_fast_with_dinner_price' => '4000',
                'Break_fast_with_lunch_price'  => '4000',
                'lunch_with_dinner_price' => '4000',
                'full_board_price' => '4000',
                'company_id' => 3
            ],


        ];

        $roomsForDemo = [
            // room for thanjavur
            // queen
            [
                'room_type_id'     => '6',
                'room_no'          => '102',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '104',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '105',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '107',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '108',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '6',
                'room_no'          => '109',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '202',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '204',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '206',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '207',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '209',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '210',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            [
                'room_type_id'     => '6',
                'room_no'          => '211',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            // castle

            [
                'room_type_id'     => '7',
                'room_no'          => '101',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '7',
                'room_no'          => '103',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '7',
                'room_no'          => '203',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '7',
                'room_no'          => '205',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],


            //king
            [
                'room_type_id'     => '8',
                'room_no'          => '303',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '305',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '308',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '309',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '8',
                'room_no'          => '310',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],


            // palace
            [
                'room_type_id'     => '9',
                'room_no'          => '106',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '9',
                'room_no'          => '208',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            //honey
            [
                'room_type_id'     => '10',
                'room_no'          => '201',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '10',
                'room_no'          => '301',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '10',
                'room_no'          => '311',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '10',
                'room_no'          => '312',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            //premium
            [
                'room_type_id'     => '11',
                'room_no'          => '304',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '11',
                'room_no'          => '306',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],

            //grand
            [
                'room_type_id'     => '12',
                'room_no'          => '302',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
            [
                'room_type_id'     => '12',
                'room_no'          => '307',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
                'company_id' => 3
            ],
        ];

        DB::table('room_types')->insert($typesForDemo);
        DB::table('rooms')->insert($roomsForDemo);
    }
}