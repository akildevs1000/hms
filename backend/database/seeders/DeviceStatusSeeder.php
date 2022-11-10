<?php

namespace Database\Seeders;

use App\Models\DeviceStatus;
use Illuminate\Database\Seeder;

class DeviceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Device::truncate();

        // $devices = [
        //     [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ], [
        //         'company_id' => '1',
        //         'branch_id' => '1',
        //         'status_id' => '1',
        //         'name' => 'main door',
        //         'short_name' => 'MED',
        //         'device_id' => 'OX-8862021010011',
        //         'ip' => '192.168.1.130',
        //         'port' => '8000',
        //         'model_number' => 'OX-8862',
        //         'device_type' => 'in',
        //     ],

        // ];

        // Device::insert($devices);

        // DeviceStatus::truncate();
        DeviceStatus::create(['name' => 'active']);
        DeviceStatus::create(['name' => 'inactive']);
    }
}