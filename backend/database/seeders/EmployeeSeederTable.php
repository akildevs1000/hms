<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ran = array('A', 'B', 'C');
        $ran[array_rand($ran, 1)];
        DB::table('assign_modules')->insert([
            'module_ids' => '[1]',
            'module_names' => '["attendance"]',
            'company_id' => '1',
        ]);

        $emp = [
            [
                'title' => 'mr',
                'employee_role_id' => '2',
                'first_name' => 'fahath',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0001',
                'joining_date' => '2022-8-20',
                'user_id' => '1',
                'system_user_id' => 'E0001',
                'role_id' => '2',
                'status' => '1',
                'department_id' => '1',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],

                // $randomElement = $ran[array_rand(array(1,2,3,4), 1)];

            ],
            [
                'title' => 'mr',
                'employee_role_id' => '2',
                'first_name' => 'francis',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0002',
                'joining_date' => '2022-8-20',
                'user_id' => '2',
                'system_user_id' => 'E0002',
                'role_id' => '2',
                'status' => '1',
                'department_id' => '1',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],
            ],
            [
                'title' => 'mr',
                'employee_role_id' => '3',
                'first_name' => 'aravind',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0003',
                'joining_date' => '2022-8-20',
                'user_id' => '3',
                'system_user_id' => 'E0003',
                'role_id' => '3',
                'status' => '1',
                'department_id' => '5',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],
            ],
            [
                'title' => 'mr',
                'employee_role_id' => '3',
                'first_name' => 'saith',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0004',
                'joining_date' => '2022-8-20',
                'user_id' => '4',
                'system_user_id' => 'E0004',
                'role_id' => '3',
                'status' => '1',
                'department_id' => '3',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],
            ],
            [
                'title' => 'mr',
                'employee_role_id' => '3',
                'first_name' => 'kumar',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0005',
                'joining_date' => '2022-8-20',
                'user_id' => '5',
                'system_user_id' => 'E0005',
                'role_id' => '3',
                'status' => '1',
                'department_id' => '3',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],
            ],
            [
                'title' => 'mr',
                'employee_role_id' => '1',
                'first_name' => 'arif',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0006',
                'joining_date' => '2022-8-20',
                'user_id' => '6',
                'system_user_id' => 'E0006',
                'role_id' => '1',
                'status' => '1',
                'department_id' => '4',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],
            ],
            [
                'title' => 'mr',
                'employee_role_id' => '4',
                'first_name' => 'ramees',
                'phone_number' => '0752348923',
                'whatsapp_number' => '0752348923',
                'employee_id' => 'E0007',
                'joining_date' => '2022-8-20',
                'user_id' => '7',
                'system_user_id' => 'E0007',
                'role_id' => '4',
                'status' => '1',
                'department_id' => '2',
                'company_id' => '1',
                'designation_id' => '1',
                'grade' => $ran[array_rand($ran, 1)],
            ],
        ];

        $user = [
            [
                'name' => 'null',
                'email' => 'fahath@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
            [
                'name' => 'null',
                'email' => 'francis@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
            [
                'name' => 'null',
                'email' => 'aravind@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
            [
                'name' => 'null',
                'email' => 'saith@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
            [
                'name' => 'null',
                'email' => 'kumar@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
            [
                'name' => 'null',
                'email' => 'arif@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
            [
                'name' => 'null',
                'email' => 'ramees@test.com',
                'password' => Hash::make('Abc@123'),
                'company_id' => '1',
                'first_login' => 1,
                'employee_role_id' => '4',
            ],
        ];

        User::insert($user);
        Employee::insert($emp);
    }
}
