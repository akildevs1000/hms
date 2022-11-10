<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Attendance::truncate();
        $daysInMonth = Carbon::now()->month(date('m'))->daysInMonth;

        $min = rand(0, 9);
        $sec = rand(0, 59);
        if ($min < 10) {
            $min = '0' . $min;
        }
        if ($sec < 10) {
            $sec = '0' . $sec;
        }

        foreach (range(1, $daysInMonth) as $day) {
            foreach (range(1, 2) as $emp_id) {
                $data =  Attendance::factory(1)->create([
                    "date" => date("Y-m-") . ($day < 10 ? '0' . $day : $day),
                    "employee_id" => $emp_id,
                    "shift_id" => Arr::random([1, 2, 3, 4, 5]),
                    "shift_type_id" => Arr::random([1, 2, 3, 4, 5]),
                    "time_table_id" => Arr::random([1, 2, 3, 4, 5]),
                    "status" => Arr::random(["A", "P"]),
                    "in" => Arr::random([$min . ':' . $sec, '---']),
                    "out" => Arr::random([$min . ':' . $sec, '---']),
                    "total_hrs" => Arr::random([$min . ':' . $sec, '---']),
                    "ot" => Arr::random([$min . ':' . $sec, '---']),
                    "late_coming" => Arr::random([$min . ':' . $sec, '---']),
                    "early_going" => Arr::random([$min . ':' . $sec, '---']),
                    "device_id_in" => Arr::random(["MED", 'BED']),
                    "device_id_out" => Arr::random(["MED", 'BED']),
                    "company_id" => 1,
                ]);
            }
        }
    }
}
