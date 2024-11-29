<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncRoomsFromOldAppToNew extends Command
{
    protected $company_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-rooms-from-old-app {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer rooms data from second connection to first connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->company_id = $this->argument("company_id");

        $data = DB::connection('second_pgsql')->table('rooms')->where("company_id", $this->company_id)->get()->toArray();

        Room::where("company_id", $this->company_id)->delete();

        foreach ($data as $d) {
            $newArrayObject = (array) $d;
            unset($newArrayObject["id"]);
            $newArrayObject["room_type_id"] = $this->getNewTypeId($newArrayObject["room_type_id"]);
            $this->info(json_encode($newArrayObject));
            Room::create($newArrayObject);
        }
    }

    public function getNewTypeId($input)
    {
        $result = match ($input) {
            12 => 146,
            11 => 147,
            9 => 148,
            21 => 149,
            6 => 150,
            7 => 151,
            10 => 152,
            8 => 153,
            default => 0,
        };

        return $result;
    }
}
