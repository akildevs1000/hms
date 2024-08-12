<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truncate:admin-expenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate the AdminExpense, AdminExpenseAttachment, and AdminExpenseItem tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('admin_expenses')->truncate();
        DB::table('admin_expense_attachments')->truncate();
        DB::table('admin_expense_items')->truncate();

        $this->info('AdminExpense, AdminExpenseAttachment, and AdminExpenseItem tables have been truncated.');

        return 0;
    }
}
