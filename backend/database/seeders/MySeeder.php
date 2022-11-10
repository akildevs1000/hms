<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // DB::table('users')->truncate();
        DB::table('expenses')->truncate();
        DB::table('rooms')->truncate();
        DB::table('room_types')->truncate();
        DB::table('id_card_types')->truncate();
        DB::table('bookings')->truncate();

        $types = [
            [
                'name'       => 'Single',
                'price'      => '1000',
                'max_person' => '1',
            ],
            [
                'name'       => 'Double',
                'price'      => '1500',
                'max_person' => '2',
            ],
            [
                'name'       => 'Triple',
                'price'      => '2000',
                'max_person' => '3',
            ],
            [
                'name'       => 'Family',
                'price'      => '4000',
                'max_person' => '4',
            ],
            [
                'name'       => 'King Sized',
                'price'      => '6500',
                'max_person' => '6',
            ],
            [
                'name'       => 'Single',
                'price'      => '1000',
                'max_person' => '1',
            ],
        ];

        $rooms = [
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
                'room_type_id'     => '4',
                'room_no'          => '106',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            //200
            [
                'room_type_id'     => '4',
                'room_no'          => '201',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '201',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '202',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '203',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '204',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '206',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '207',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '208',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            // 300
            [
                'room_type_id'     => '4',
                'room_no'          => '301',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '302',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '303',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '304',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '305',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '306',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '307',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '308',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            // 400

            [
                'room_type_id'     => '4',
                'room_no'          => '401',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '402',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '403',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '404',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '405',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '406',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '407',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            [
                'room_type_id'     => '4',
                'room_no'          => '408',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],

            //500

            [
                'room_type_id'     => '4',
                'room_no'          => '501',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
            [
                'room_type_id'     => '4',
                'room_no'          => '502',
                'check_in_status'  => '0',
                'check_out_status' => '0',
                'deleteStatus'     => '0',
            ],
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

        $ids = [
            [
                'name' => 'National Identity Card',
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
        ];

        $bookings = [
            [
                'customer_id'     => 1,
                'room_id'         => 1,
                'booking_date'    => "2022-11-09",
                'check_in'        => "2022-11-09",
                'check_out'       => "2022-11-14",
                'total_price'     => "5000",
                'remaining_price' => "4000",
                'payment_status'  => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'customer_id'     => 1,
                'room_id'         => 2,
                'booking_date'    => "2022-11-09",
                'check_in'        => "2022-11-09",
                'check_out'       => "2022-11-14",
                'total_price'     => "3000",
                'remaining_price' => "1500",
                'payment_status'  => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'customer_id'     => 2,
                'room_id'         => 3,
                'booking_date'    => "2022-11-09",
                'check_in'        => "2022-11-09",
                'check_out'       => "2022-11-14",
                'total_price'     => "6000",
                'remaining_price' => "3000",
                'payment_status'  => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'customer_id'     => 2,
                'room_id'         => 4,
                'booking_date'    => "2022-11-09",
                'check_in'        => "2022-11-09",
                'check_out'       => "2022-11-14",
                'total_price'     => "5000",
                'remaining_price' => "4000",
                'payment_status'  => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'customer_id'     => 2,
                'room_id'         => 5,
                'booking_date'    => "2022-11-09",
                'check_in'        => "2022-11-09",
                'check_out'       => "2022-11-14",
                'total_price'     => "5000",
                'remaining_price' => "4000",
                'payment_status'  => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ];

        $Expenses = [
            [
                'item'   => 'staff food 3 time',
                'amount' => "540",
            ],
            [
                'item'   => 'staff tea',
                'amount' => "190",
            ],
            [
                'item'   => 'comp.tea & coffee ',
                'amount' => "60",
            ],
            [
                'item'   => 'sun direct recharge 2 rooms',
                'amount' => "462",
            ],
        ];

        $customers = [];

        foreach (range(1, 50) as $row) {
            $customers[] = [
                'name'            => 'Customer' . $row,
                'contact_no'      => rand(1, 11111111111),
                'email'           => 'customer@example.com',
                'id_card_type_id' => '1',
                'id_card_no'      => rand(1, 5),
                'car_no'          => rand(1000, 9999),
                'no_of_adult'     => rand(1, 5),
                'no_of_child'     => rand(1, 5),
                'no_of_baby'      => rand(1, 5),
                'address'         => 'test address',
                'company_id'      => 2,
            ];
        }

        $users = [
            [
                'name'     => 'admin',
                'email'    => "admin@admin.com",
                'password' => Hash::make('admin'),
            ],
        ];

        DB::table('expenses')->insert($Expenses);
        DB::table('rooms')->insert($rooms);
        DB::table('room_types')->insert($types);
        DB::table('id_card_types')->insert($ids);
        DB::table('bookings')->insert($bookings);
        DB::table('users')->insert($users);
        DB::table('customers')->insert($customers);
    }
}
