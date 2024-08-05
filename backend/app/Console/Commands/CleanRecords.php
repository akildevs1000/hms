<?php

namespace App\Console\Commands;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\OrderRoom;
use App\Models\SubCustomer;
use App\Models\Transaction;
use Illuminate\Console\Command;

class CleanRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arr = [
            "booking" => Booking::truncate(),
            "booked_rooms" => BookedRoom::truncate(),
            "order_rooms" => OrderRoom::truncate(),
            "transaction" => Transaction::truncate(),
            "customer" => Customer::truncate(),
            "sub_customer" => SubCustomer::truncate(),
        ];

        $this->info(json_encode(implode(",", array_keys($arr)), JSON_PRETTY_PRINT));
    }
}
