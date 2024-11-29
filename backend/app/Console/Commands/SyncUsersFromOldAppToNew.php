<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUsersFromOldAppToNew extends Command
{
    protected $company_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-users-from-old-app {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer users data from second connection to first connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->company_id = $this->argument("company_id");

        $data = DB::connection('second_pgsql')->table('companies')->where("id", $this->company_id)->get()->toArray();

        // User::where("company_id", $this->company_id)->delete();

        foreach ($data as $d) {
            $newArrayObject = (array) $d;
            unset($newArrayObject["id"]);
            $newArrayObject["user_id"] = 4;
            $this->info(json_encode($newArrayObject));
            Company::create($newArrayObject);
        }
    }
}
