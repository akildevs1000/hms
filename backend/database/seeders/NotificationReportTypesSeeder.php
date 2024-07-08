<?php

namespace Database\Seeders;

use App\Models\NotificationReportTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationReportTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NotificationReportTypes::create([
            'name' => 'Night Audit',
            'description' => 'A Night Audit report summary.',
            'company_id' => 3,
        ]);
    }
}
