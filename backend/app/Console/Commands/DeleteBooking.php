<?php

namespace App\Console\Commands;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\OrderRoom;
use App\Models\Payment;
use App\Models\Posting;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-booking {company_id} {reservation_no}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes a booking and all associated records by reservation number.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Booking::truncate();
        // Payment::truncate();
        // Transaction::truncate();
        // OrderRoom::truncate();
        // BookedRoom::truncate();
        // Posting::truncate();
        // Customer::truncate();
        // return 0;

        $companyId = $this->argument('company_id');
        $reservationNo = $this->argument('reservation_no');

        $booking = Booking::where("company_id", $companyId)->where('reservation_no', $reservationNo)->first();

        if (!$booking) {
            $this->error("Booking with reservation number {$reservationNo} not found.");
            return 1;
        }

        DB::beginTransaction();

        try {
            // Delete associated records
            Payment::where('booking_id', $booking->id)->delete();
            Transaction::where('booking_id', $booking->id)->delete();
            OrderRoom::where('booking_id', $booking->id)->delete();
            BookedRoom::without(['postings', 'booking'])->where('booking_id', $booking->id)->delete();
            Posting::where('booking_id', $booking->id)->delete();

            // Delete booking itself
            $booking->delete();

            DB::commit();

            $this->info("Booking and associated records for reservation number {$reservationNo} have been deleted successfully.");
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Failed to delete booking: " . $e->getMessage());
            return 1;
        }
    }
}
