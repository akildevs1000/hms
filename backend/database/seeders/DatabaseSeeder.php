<?php

namespace Database\Seeders;

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
        $this->call([FoodTableSeeder::class]);
        $this->call([PaymentSeeder::class]);
        $this->call([CountriesTableSeeder::class]);
        $this->call([SourceTableSeeder::class]);
    }
}