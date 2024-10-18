<?php

namespace App\Jobs;

use App\Http\Controllers\ReportGenerateController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReportForCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id, $date)
    {
        $this->company_id = $company_id;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new ReportGenerateController)->processData($this->company_id, $this->date);
    }
}
