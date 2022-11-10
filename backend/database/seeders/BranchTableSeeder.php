<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 3; $i++) {
            $employee = Branch::create([
                "company_id" => Company::all()->random()->id,
                "user_id" => User::all()->random()->id,
                "name" => 'test branch',
                "expiry" => now(),
                "max_employee" => 10,
                "max_devices" => 5,
                "location" => 'dubai',
                "member_from" => now(),
            ]);
        }
    }
}
