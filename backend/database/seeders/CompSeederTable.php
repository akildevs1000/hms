<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompSeederTable extends Seeder
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
            'name' => "company",
            'password' => Hash::make("Abc@123"),
            'email' => "company1@hrms.com",
            'role_id' => $role->id,
            'is_master' => 1,
        ]);

        $company = Company::create([
            'name' => "akil security & alarm systems",
            'member_from' => "1982-09-04",
            'expiry' => "2012-10-24",
            'max_branches' => "2",
            'max_employee' => "2",
            'max_devices' => "2",
            'location' => "demo location",
            'lat' => "11111",
            'lon' => "11111",
            'company_code' => "AE0001",
            'user_id' => $user->id,
        ]);

        $user->company_id = $company->id;
        $user->save();

        $companyContact = CompanyContact::create([
            'company_id' => $company->id,
            'name' => "akil security & alarm systems llc",
            'number' => "11111111",
            'position' => "demo contact position",
            'whatsapp' => "22222222",
        ]);
    }
}
