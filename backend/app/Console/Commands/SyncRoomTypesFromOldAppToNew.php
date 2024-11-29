<?php

namespace App\Console\Commands;

use App\Models\RoomType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncRoomTypesFromOldAppToNew extends Command
{
    protected $company_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-room-types-from-old-app {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer room types data from second connection to first connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->company_id = $this->argument("company_id");

        $data = DB::connection('second_pgsql')->table('room_types')->where("company_id", $this->company_id)->get()->toArray();

        // echo count($data);

        // die;


        // RoomType::where("company_id", $this->company_id)->delete();

        foreach ($data as $d) {
            $newArrayObject = (array) $d;
            // unset($newArrayObject["id"]);
            $this->info(json_encode($newArrayObject));
            RoomType::create($newArrayObject);
            // DB::connection('second_pgsql')->table('room_types')
        }
    }
}
