<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $des = [
            [
                'name' => 'software engineer',
                'company_id' => '1',
                'branch_id' => '1',
                'department_id' => '1',
            ],
            [
                'name' => 'ceo',
                'company_id' => '1',
                'branch_id' => '1',
                'department_id' => '4',
            ],
            [
                'name' => 'network technician',
                'company_id' => '1',
                'branch_id' => '1',
                'department_id' => '3',
            ],
            [
                'name' => 'salesman',
                'company_id' => '1',
                'branch_id' => '1',
                'department_id' => '2',
            ],
            [
                'name' => 'marketing specialist',
                'company_id' => '1',
                'branch_id' => '1',
                'department_id' => '5',
            ],

        ];

        Designation::insert($des);

    }
}
