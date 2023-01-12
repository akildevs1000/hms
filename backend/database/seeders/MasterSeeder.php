<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                'name' => 'master',
                'email' => 'master@ezhms.com',
                'password' => Hash::make('secret'),
                'is_master' => 1,
            ],
        );

        //hyderspark thanjavur

        $userThanj =    User::create([
            'name' => 'ignore',
            'email' => 'Hyderspark@outlook.com',
            'password' => Hash::make('Abc@123'),
            'is_master' => true,
            'role_id' => 1,
            'company_id' => 1,
            'branch_id' => 0,
            'employee_role_id' => 0,
            'first_login' => true,
        ]);

        Company::create([
            'id' => 1,
            'user_id' => $userThanj->id,
            'name' => 'thanjavur',
            'member_from' => '2023-01-27',
            'expiry' => '2023-01-27',
            'max_employee' => 100000,
            'max_devices' => 1,
            'location' => 'thanjavur',
            'logo' => '1673109140.jpg',
            'p_o_box_no' => null,
            'mol_id' => null,
            'company_code' => '1',
            'no_branch' => 'false',
            'max_branches' => '1',
            'lat' => '1212',
            'lon' => '2323'
        ]);

        //hyderspark kodai
        $userKodai =    User::create([
            'name' => 'ignore',
            'email' => 'Hyderskodai@gmail.com',
            'password' => Hash::make('Abc@123'),
            'is_master' => true,
            'role_id' => 1,
            'company_id' => 2,
            'branch_id' => 0,
            'employee_role_id' => 0,
            'first_login' => true,
        ]);

        Company::create([
            'id' => 2,
            'user_id' => $userKodai->id,
            'name' => 'Kodai',
            'member_from' => '2023-01-27',
            'expiry' => '2023-01-27',
            'max_employee' => 100000,
            'max_devices' => 1,
            'location' => 'Kodai',
            'logo' => '1673109140.jpg',
            'p_o_box_no' => null,
            'mol_id' => null,
            'company_code' => '1',
            'no_branch' => 'false',
            'max_branches' => '1',
            'lat' => '1212',
            'lon' => '2323'
        ]);

        //hyderspark kodai
        $userDemo =    User::create([
            'name' => 'ignore',
            'email' => 'demo@gcompany.com',
            'password' => Hash::make('Abc@123'),
            'is_master' => true,
            'role_id' => 1,
            'company_id' => 3,
            'branch_id' => 0,
            'employee_role_id' => 0,
            'first_login' => true,
        ]);

        Company::create([
            'id' => 3,
            'user_id' => $userDemo->id,
            'name' => 'Demo',
            'member_from' => '2023-01-27',
            'expiry' => '2023-01-27',
            'max_employee' => 100000,
            'max_devices' => 1,
            'location' => 'Demo',
            'logo' => '1673109140.jpg',
            'p_o_box_no' => null,
            'mol_id' => null,
            'company_code' => '1',
            'no_branch' => 'false',
            'max_branches' => '1',
            'lat' => '1212',
            'lon' => '2323'
        ]);
    }
}