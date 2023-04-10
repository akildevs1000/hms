<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HallTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoomType::create(
            [
                'name' => 'Jasmin',
                'price' => '6000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '6000',
                'Break_fast_price' => '6000',
                'Break_fast_with_dinner_price' => '6000',
                'Break_fast_with_lunch_price'  => '6000',
                'lunch_with_dinner_price' => '6000',
                'full_board_price' => '6000',
                'company_id' => 1
            ],
            [
                'name' => 'Thalam',
                'price' => '12000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '12000',
                'Break_fast_price' => '12000',
                'Break_fast_with_dinner_price' => '12000',
                'Break_fast_with_lunch_price'  => '12000',
                'lunch_with_dinner_price' => '12000',
                'full_board_price' => '12000',
                'company_id' => 1
            ],
            [
                'name' => 'Jasmin',
                'price' => '6000',
                'adult' => '2',
                'child' => '2',
                'baby' => '1',
                'max_person' => '5',
                'room_only_price'   => '6000',
                'Break_fast_price' => '6000',
                'Break_fast_with_dinner_price' => '6000',
                'Break_fast_with_lunch_price'  => '6000',
                'lunch_with_dinner_price' => '6000',
                'full_board_price' => '6000',
                'company_id' => 2
            ],
        );

        // $thanHallThalamId =   RoomType::where('name', 'Thalam')->where('company_id', 1)->first();
        // $thanHallJasminId =   RoomType::where('name', 'Jasmin')->where('company_id', 1)->first();
        // $kodHallJasminId =   RoomType::where('name', 'Jasmin')->where('company_id', 2)->first();


        // Room::create(
        //     [
        //         'room_type_id'     => $thanHallThalamId->id,
        //         'room_no'          => '900',
        //         'check_in_status'  => '0',
        //         'check_out_status' => '0',
        //         'deleteStatus'     => '0',
        //         'company_id' => 1
        //     ],
        //     [
        //         'room_type_id'     => $thanHallJasminId,
        //         'room_no'          => '901',
        //         'check_in_status'  => '0',
        //         'check_out_status' => '0',
        //         'deleteStatus'     => '0',
        //         'company_id' => 1
        //     ],
        //     [
        //         'room_type_id'     => $kodHallJasminId,
        //         'room_no'          => '900',
        //         'check_in_status'  => '0',
        //         'check_out_status' => '0',
        //         'deleteStatus'     => '0',
        //         'company_id' => 2
        //     ],
        // );
    }
}