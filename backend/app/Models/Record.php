<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    const SUMMARY = "summary";
    const CASH = "cash";
    const OTA = "ota";

    protected $guarded = [];

    protected $casts = ['data' => 'array'];

    

    public function getCityLedger($date, $companyId)
    {
        return Payment::query()
            ->where('is_city_ledger', 1)
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->sum("amount") ?? 0;
    }

    public function getAllIncome($date, $companyId)
    {
        return Payment::query()
            ->where('is_city_ledger', 0)
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', 2);
            })
            ->whereHas('paymentMode', fn($q) => $q->where('id', "!=", PaymentMode::CITYLEDGER))
            ->sum("amount") ?? 0;
    }

    public function getIncomeByPaymentType($date, $companyId, $payment_mode)
    {
        return Payment::query()
            ->where('is_city_ledger', 0)
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->whereHas('booking', function ($q) {
                $q->where('booking_status', 2);
            })
            ->whereHas('paymentMode', fn($q) => $q->where('id', $payment_mode))
            ->sum("amount") ?? 0;
    }

    public function getExpense($date, $companyId)
    {
        return AdminExpense::query()
            ->where('is_admin_expense', AdminExpense::NonManagementExpense)
            ->where('company_id', $companyId)
            ->whereDate('bill_date', $date ?? date("Y-m-d"))
            ->sum("total") ?? 0;
    }
}
