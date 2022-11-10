<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = [
            ['name' => 'ceo'],
            ['name' => 'engineer'],
            ['name' => 'technician'],
            ['name' => 'sale'],
            ['name' => 'admin'],
            ['name' => 'company'],
            ['name' => 'manager'],
            ['name' => 'editor'],
            ['name' => 'user'],
        ];

        Role::insert($role);
    }
}
