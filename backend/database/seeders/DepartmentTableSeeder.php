<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // User::all()->random();

        $dep = [
            [
                'name' => 'it',
                'company_id' => '1',
                'branch_id' => '1',
            ],
            [
                'name' => 'sales',
                'company_id' => '1',
                'branch_id' => '1',
            ],
            [
                'name' => 'technical',
                'company_id' => '1',
                'branch_id' => '1',
            ],
            [
                'name' => 'management',
                'company_id' => '1',
                'branch_id' => '1',
            ],
            [
                'name' => 'marketing',
                'company_id' => '1',
                'branch_id' => '1',
            ],
            [
                'name' => 'purchase',
                'company_id' => '1',
                'branch_id' => '1',
            ],
            [
                'name' => 'account',
                'company_id' => '1',
                'branch_id' => '1',
            ],

        ];

        Department::insert($dep);

    }
}
