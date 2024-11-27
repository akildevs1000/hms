<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCustomerFromOldAppToNew extends Command
{
    protected $company_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-customer-from-old-app {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer customer data from second connection to first connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->company_id = $this->argument("company_id");

        $customers = DB::connection('second_pgsql')->table('customers')
            ->select('customer_type', 'title', 'name', 'first_name', 'last_name', 'contact_no', 'whatsapp', 'email', 'nationality', 'dob', 'company_id', 'address')
            ->distinct('contact_no');

        $this->info($this->company_id);

        // Define the chunk size (for example, 500 records at a time)
        $chunkSize = 2000;

        // Fetch data in chunks from the second connection
        $customers->orderBy('contact_no')  // Ensure records are ordered by 'id' or another unique field
            ->chunk($chunkSize, function ($customers) {
                // Filter out already existing customers from the first connection
                $insertData = $customers->map(function ($customer) {
                    return [
                        'customer_type' => $customer->customer_type,
                        'title' => $customer->title,
                        'name' => $customer->name,
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name,
                        'contact_no' => $customer->contact_no,
                        'whatsapp' => $customer->whatsapp,
                        'email' => $customer->email,
                        'nationality' => $customer->nationality,
                        'dob' => $customer->dob,
                        'company_id' => $this->company_id,
                        'address' => $customer->address,
                    ];
                })->toArray();

                // Insert data into the first connection (default connection)
                DB::beginTransaction();
                try {
                    $this->info("Transferred " .  count($insertData) . " new customers.");
                    DB::table('customers')->insert($insertData);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error('Error: ' . $e->getMessage());
                }
            });
    }
}
