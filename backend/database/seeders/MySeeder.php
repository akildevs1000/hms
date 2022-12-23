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
        DB::table('id_card_types')->truncate();
        DB::table('bookings')->truncate();



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

        // DB::table('expenses')->insert($Expenses);
        DB::table('id_card_types')->insert($ids);
        // DB::table('bookings')->insert($bookings);
        DB::table('users')->insert($users);
        // DB::table('customers')->insert($customers);
    }
}