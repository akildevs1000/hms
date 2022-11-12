<?php

namespace Database\Seeders;

use Database\Seeders\CompSeederTable;
use Database\Seeders\DepartmentTableSeeder;
use Database\Seeders\DesignationsTableSeeder;
use Database\Seeders\EmployeeSeederTable;
use Database\Seeders\MasterSeeder;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShiftTypeTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Production
        $this->call([MasterSeeder::class]);
        $this->call([PermissionSeeder::class]);
        $this->call([ModuleSeeder::class]);
        $this->call([RoomsTableSeeder::class]);

        // local
        if (env('APP_ENV') == 'local') {
            $this->call([MySeeder::class]);
            $this->call([RoleSeeder::class]);
            $this->call([DepartmentTableSeeder::class]);
            $this->call([DesignationsTableSeeder::class]);
            $this->call([EmployeeSeederTable::class]);
            // $this->call([CompSeederTable::class]);
        }
    }
}