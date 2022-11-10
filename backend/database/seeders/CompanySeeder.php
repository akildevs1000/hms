<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrCreate(['name' => 'company']);

        $user = User::create([
            'name' => "demo company account",
            'password' => Hash::make("secret"),
            'email' => "company1@hrms.com",
            'role_id' => $role->id,
            'is_master' => 1,
        ]);

        $company = Company::create([
            'name' => "demo company",
            'member_from' => "1982-09-04",
            'expiry' => "2012-10-24",
            'max_branches' => "2",
            'max_employee' => "2",
            'max_devices' => "2",
            'location' => "demo location",
            'lat' => "11111",
            'lon' => "11111",
            'company_code' => "1",
            'user_id' => $user->id,
        ]);

        $companyContact = CompanyContact::create([
            'company_id' => $company->id,
            'name' => "demo contact name",
            'number' => "11111111",
            'position' => "demo contact position",
            'whatsapp' => "22222222",
        ]);

        $arr = [
            [
                'company_id' => $company->id,
                'name' => "sales",
            ],
            [
                'company_id' => $company->id,
                'name' => "purchase",
            ],
            [
                'company_id' => $company->id,
                'name' => "marketing",
            ],
        ];

        Department::insert($arr);

        $departments = Department::select("id", "name")->get();

        foreach ($departments as $department) {

            $arr = [
                [
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'name' => $department->name . " manager",
                ],
                [
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'name' => $department->name . " officer",
                ],
            ];
            Designation::insert($arr);
        }
    }
}
