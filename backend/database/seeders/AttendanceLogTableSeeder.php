<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\AttendanceLog;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttendanceLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::truncate();
        AttendanceLog::truncate();




        // for ($i = 0; $i <= 2; $i++) {

        //     $employee = Employee::create(
        //         [
        //             "first_name" => "jhone",
        //             "last_name" => "michel",
        //             "company_id" => 1,
        //             "employee_id" => $i,

        //         ],
        //         [
        //             "first_name" => "marvel",
        //             "last_name" => "clark",
        //             "company_id" => 1,
        //             "employee_id" => $i,
        //         ],
        //         [
        //             "first_name" => "peter",
        //             "last_name" => "parker",
        //             "company_id" => 1,
        //             "employee_id" => $i,

        //         ]
        //     );
        // }




        $start = strtotime("10 april 2022");
        $end = strtotime("01 aug 2022");

        // $ids = Employee::pluck("employee_id");

        // foreach ($ids as $id) {
        //     $date_range = mt_rand($start, $end);
        //     $date = date("Y-m-d H:i:s", $date_range);

        //     $employee = AttendanceLog::create([
        //         "UserID" => $id,
        //         "DeviceID" => "OX-8862021010011" . $id,
        //         "LogTime" => $date,
        //         "company_id" => 1
        //     ]);
        // }


        for ($i = 1; $i < 5000; $i++) {
            $date_range = mt_rand($start, $end);
            $date = date("Y-m-d H:i:s", $date_range);

            $employee = AttendanceLog::create([
                "UserID" => $i,
                "DeviceID" => "OX-8862021010011" . $i,
                "LogTime" => $date,
                "company_id" => 1
            ]);
        }
    }
}
