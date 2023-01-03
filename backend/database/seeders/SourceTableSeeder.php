<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Source::truncate();

        // ============Agent==============
        $agents =  [
            'TRAVEL AGENTS ',
            'SOUTHERN TRAVELS ',
            'OPTIMUS TRAVELS ',
            'EMPEROR TRAVELINE',
            'KNOCK HOLIDAYS ',
            'CLASSIC TOURS',
            'CHOLAMADALAM TOURS & TRAVELS',
            'DREAM STAY HOLIDAY',
            'FSR TRAVELS PVT LTD',
            'GOSH TOURS & TRAVELS',
            'HOLY INDIA TRAVELS PVT LTD',
            'HONEST TOURS & TRAVELS',
            'KARTHIK TRAVELS COIMBATORE ',
            'KERALA NATURE HOLIDAY ',
            'KL CHENNAI TOURISM ',
            'KHAYAS HOLIDAYS',
            'GOGULAM TRAVELS & TOURS ',
            'MATRIX HOLIDAYS ',
            'MAYURA TOURS & TRAVELS ',
            'ROYAL VACATIONS ',
            'SHASHA HOLIDAYS',
            'SHIVA TURQUOISE HOLIDAYS ',
            'YATHRI TOURS & TRAVELS ',
            'SOUTH INDIA TOURS ',
            'SOUTH GURU HOLIDAYS ',
            'SRI KUMARAVEL TRAVELS ',
            'SUN TOURISM ',
            'THE INDIAN JOURNEYS',
            'TRICHY VENKATESHWARA TRAVELS',
            'TIRITHRA TOURS & TRAVELS',
            'VATHIKA INTERNATIONAL TRAVELS',
            'YES TRAVELS HOLIDAYS',
            'SONAL HOLIDAYS',
            'TRIP GAIN TRAVELS',
            'VRUDDHI TRAVELS',
            'S.K TRAVEL',
        ];

        foreach ($agents as $agent) {
            $data = [
                'name' => $agent,
                'type' => 'agent',
                'company_id' => '1',
            ];
            Source::create($data);
        }
        // ============End Agent==============

        // ============Online==============
        $online = [
            'GOIBIBO/MMT(MAKE MY TRIP)',
            'BOOKING.COM',
            'CLEARTRIP.COM',
            'AGODA.COM',
            'EXPEDIA.COM',
            'YATRA.COM',
            'EASYMYTRIP.COM',
            'TRIPADVISOR.COM'
        ];

        foreach ($online as $onl) {
            $kodai = [
                'name' => $onl,
                'type' => 'online',
                'company_id' => '2',
            ];
            Source::create($kodai);

            $thanj = [
                'name' => $onl,
                'type' => 'online',
                'company_id' => '1',
            ];
            Source::create($thanj);
        }

        // ============End Online==============

    }
}