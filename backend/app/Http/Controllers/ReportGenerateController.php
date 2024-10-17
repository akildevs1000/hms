<?php

namespace App\Http\Controllers;

use App\Console\Commands\AuditReport;
use App\Models\AdminExpense;
use App\Models\AdminExpenseItem;
use App\Models\AuditHistory;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\CancelRoom;
use App\Models\Company;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportGenerateController extends Controller
{
    public function generateAuditReport()
    {
        $company_ids = $this->getNotificationCompanyIds();
        $date = date('Y-m-d', strtotime('yesterday')); // Use yesterday's date
        //$date = date('Y-07-01');

        foreach ($company_ids as $company_id) {
            $model = Booking::query();
            echo $this->processData($company_id, $model, $date, 'Today Checkin Report', 1);
        }

        return true;
    }
    public function processData($company_id, $date)
    {

        $request = array(
            'company_id' => $company_id,
            'date' => $date,
        );
        $request = (object) $request;

        $todayCheckin  = $this->todayCheckinAudit($request);
        $continueRooms = $this->continueAudit($request);
        $todayCheckOut = $this->todayCheckOutAudit($request);
        $todayPayments = $this->todayPaymentsAudit($request);
        $cityLedgerPaymentsAudit = $this->cityLedgerPaymentsAudit($request);
        $cancelRooms = $this->cancelRooms($request);

        $guestArray = [
            'checkin' => $todayCheckin->count(),
            'continue' => $continueRooms->count(),
            'dayuse' => $todayCheckOut->count(),
            'complementary' => $todayCheckOut->count(),
            'checkout' => $todayCheckOut->count(),
            'closing' => 0,
        ];

        // $guestArray["total"] = array_sum(array_values($guestArray));

        $summary = [
            'room' => $guestArray,
            'guest' => $this->processFoodOrderList($this->foodAudit($request)->get()->toArray()),
            'occupied' => $this->getOccupied($request)->count(),
            'income' => $this->getIncome($request),
            'expense' => $this->getExpense($request),
            'managementExpense' => $this->getExpense($request, AdminExpense::ManagementExpense),
            "calculateBookingsBySource" => $this->calculateBookingsBySource($request),
        ];

        $arr = [];

        $combinedExpenses = $summary["expense"]["Total"] + $summary["managementExpense"]["Total"];

        $summary["profit"] = $summary["income"]["Total"] + $combinedExpenses;

        $profit = $summary["income"]["Total"] - $combinedExpenses;
        $loss = $summary["income"]["Total"] - $combinedExpenses;

        $summary["profit_loss"] = [
            "lost" => $loss > 0 ? 0 : $loss,
            "profit" => $profit < 0 ? 0 : $profit,
            "CityLedger" => $summary["income"]["CityLedger"]
        ];



        // $this->processPayload("summary", "Summary", $date, $company_id, $summary, "summary");

        return $pdf = Pdf::loadView('report.audit.' . "summary", ['data' => $summary, 'company' => Company::find($company_id), 'fileName' => "Summary", 'date' => $date])
            ->setPaper('a4', 'landscape')->stream();

        // AuditHistory::where("company_id", $company_id)->whereDate("created_at", $date)->delete();

        // $this->processPayload("check_in", "Today Check-in Report", $date, $company_id, $todayCheckin->get(), "today_check_in");
        // $this->processPayload("continue", "Continue Report", $date, $company_id, $continueRooms->get(), "continue_report");
        // $this->processPayload("check_out", "Check-out Report", $date, $company_id, $todayCheckOut->get(), "check_out_report");
        // $this->processPayload("payment", "Today Booking Report", $date, $company_id, $todayPayments->get(), "today_booking_report");
        // $this->processPayload("cityLedger", "City Ledger Report", $date, $company_id, $cityLedgerPaymentsAudit->get(), "city_ledger_report");
        // $this->processPayload("cancel", "Cancel Rooms Report", $date, $company_id, $cancelRooms->get(), "cancel_rooms");
        // $this->processPayload("food", "Food Order list", $date, $company_id, $this->foodAudit($request)->get(), "food_order_list");

        $arr = [
            "type" => "expense",
            "file_name" => "---",
            "file_path" => "---",
            'data' => number_format($this->getExpense($request)["total"], 2, '.', ''),
            'company_id' => $company_id,
            'dateTime' => date("d M y h:i:s"),
        ];

        AuditHistory::create($arr);


        return 'Reports are  generated successfully ' . $company_id . '.\n';
    }

    public function processPayload($type, $fileName, $date, $company_id, $data, $bladeView)
    {

        $pdf = Pdf::loadView('report.audit.' . $bladeView, ['data' => $data, 'company' => Company::find($company_id), 'fileName' => $fileName, 'date' => $date])
            ->setPaper('a4', 'landscape')->output();
        $file_path  = "pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf';
        $arr = [
            "type" => $type,
            "file_name" => $fileName,
            "file_path" => $file_path,
            'data' => $data,
            'company_id' => $company_id,
            'dateTime' => date("d M y h:i:s"),
        ];

        AuditHistory::create($arr);
        Storage::disk('local')->put($file_path, $pdf);
    }
    public function getNotificationCompanyIds()
    {
        return Company::orderBy('id', 'asc')->pluck("id");
    }

    private function todayCheckinAudit($request)
    {
        $company_id = $request->company_id;
        return
            Booking::where(function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', $request->date);
            })
            ->with('customer:id,first_name')
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')->with('transactions', function ($q) use ($request) {
                $q->where('is_posting', 0);
                // $q->where('credit', '>', 0);
                $q->whereDate('date', $request->date);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            });
    }

    private function continueAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
            $q->where('booking_status', '!=', -1);
            $q->where('booking_status', '=', 2);
            $q->where('company_id', $company_id);
            $q->whereDate('check_in', '<', $request->date);
        })
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            });
    }

    private function todayCheckOutAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
            $q->whereIn('booking_status', [0, 3, 4]);
            $q->where('booking_status', '!=', -1);
            $q->where('company_id', $company_id);
            $q->whereDate('check_out', $request->date);
        })
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')
            ->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            });
    }

    private function todayPaymentsAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
            $q->whereIn('booking_status', [1]);
            $q->where('booking_status', '!=', -1);
            $q->where('paid_amounts', '>', 0);
            $q->where('company_id', $company_id);
        })
            ->whereHas('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('payment_method_id', '!=', 7);
            })
            ->withSum(['transactions' => function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            }], 'credit')
            ->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            });
    }

    private function cancelRooms($request)
    {
        $company_id = $request->company_id;
        return CancelRoom::query()
            ->with('user')
            ->whereDate('created_at', $request->date)
            ->where('company_id', $company_id)
            ->with('booking:id,reservation_no,created_at');
        //->get(['booking_id', 'room_no', 'room_type', 'grand_total', 'reason', 'cancel_by', 'created_at', 'action', 'check_in', 'status_before_cancelation', 'status_before_cancelation_msg']);
    }

    private function cityLedgerPaymentsAudit($request)
    {
        $company_id = $request->company_id;
        return Booking::where(function ($q) use ($company_id, $request) {
            $q->whereIn('booking_status', [0]);
            $q->where('booking_status', '!=', -1);
            $q->where('paid_amounts', '>', 0);
            $q->where('company_id', $company_id);
            $q->whereDate('check_out', '<', $request->date);
            // $q->whereDate('check_out', '!=', $request->date);
        })
            ->whereHas('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                // $q->where('credit', '>', 0);
                $q->where('payment_method_id', '!=', 7);
            })
            ->with('customer:id,first_name')
            ->with('transactions', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
                $q->where('credit', '>', 0);
                $q->where('is_posting', 0);
                $q->where('payment_method_id', '!=', 7);
                $q->where('company_id', $request->company_id)
                    ->with('paymentMode');
            });
    }

    private function foodAudit($request)
    {
        return BookedRoom::whereCompanyId($request->company_id)
            ->where(function ($query) use ($request) {
                $query->whereDate('check_out', $request->date)
                    ->orWhereDate('check_in', $request->date);
            })
            ->whereIn('booking_status', [1, 2])
            ->without('booking')
            ->with("foodplan");
    }

    public function processFoodOrderList($bookedRooms)
    {
        // Initialize totals
        $totals = [
            'breakfast' => 0,
            'lunch' => 0,
            'dinner' => 0,
        ];

        $items = [];

        foreach ($bookedRooms as $bookedRoom) {

            $breakfast =  $bookedRoom["foodplan"]["breakfast"] ?? 0;
            $lunch =  $bookedRoom["foodplan"]["lunch"] ?? 0;
            $dinner =  $bookedRoom["foodplan"]["dinner"] ?? 0;

            if (!$bookedRoom["foodplan"]["is_for_hall"]) {

                // Calculate total meals for the current room
                $item['breakfast'] = (
                    $bookedRoom["no_of_adult"] * $breakfast +
                    $bookedRoom["no_of_child"] * $breakfast +
                    $bookedRoom["no_of_baby"] * $breakfast
                );

                $item['lunch'] = (
                    $bookedRoom["no_of_adult"] * $lunch +
                    $bookedRoom["no_of_child"] * $lunch +
                    $bookedRoom["no_of_baby"] * $lunch
                );

                $item['dinner'] = (
                    $bookedRoom["no_of_adult"] * $dinner +
                    $bookedRoom["no_of_child"] * $dinner +
                    $bookedRoom["no_of_baby"] * $dinner
                );

                // Add the current room's meal totals to the overall totals
                $totals['breakfast'] += $item['breakfast'];
                $totals['lunch'] += $item['lunch'];
                $totals['dinner'] += $item['dinner'];
            } else {
                $totals[$bookedRoom['room_no']] = $bookedRoom['no_of_adult'];
            }

            $items[] = $item;
        }

        $totals["total"] = array_sum(array_values($totals));

        return $totals;
    }

    public function getOccupied($request)
    {
        $company_id = $request->company_id;
        $todayDate = $request->date;

        return Room::whereHas('bookedRoom', function ($q) use ($company_id, $todayDate) {
            $q->whereNotNull('room_id');
            $q->where('company_id', $company_id);


            $q->where(function ($query) use ($todayDate) {
                // Check if the check-in is before or equal to today, and check-out is after or equal to today
                $query->whereDate('check_in', '<=', $todayDate)
                    ->whereDate('check_out', '>=', $todayDate)
                    ->where('booking_status', BookedRoom::BOOKED) // Status for dirty rooms
                    ->where('booking_status', '!=', 0); // Exclude non-active bookings
            });
        });
    }

    public function getIncome($request)
    {
        $modes = PaymentMode::pluck("name")->map(fn($mode) => str_replace(' ', '', $mode))->toArray();
        $stats = [];

        foreach ($modes as $mode) {
            $stats[$mode] = 0;
        }

        $data =   Payment::query()
            ->where('company_id', $request->company_id)
            ->whereDate('date', $request->date)
            ->get();

        foreach ($data as $item) {

            $payment_type =  $item->payment_type;

            $paymentTypeName = str_replace(' ', '', $payment_type->name ?? "") ?? "";

            foreach ($modes as $mode) {
                $item[$mode] = 0;
            }

            if ($payment_type && $paymentTypeName  && in_array($paymentTypeName, $modes)) {
                $item[$paymentTypeName] = $item->amount;
                $stats[$paymentTypeName] += $item->amount;
            }
        }

        $stats["Total"] = array_sum($stats) - $stats["CityLedger"];

        return $stats;
    }


    public function getExpense($request, $is_admin_expense = 0)
    {
        $company_id = $request->company_id;
        $date = $request->date;
        $expenseModes = [
            'Cash' => AdminExpense::CASH,
            'Card' => AdminExpense::CARD,
            'Online' => AdminExpense::ONLINE,
            'Bank' => AdminExpense::BANK,
            'UPI' => AdminExpense::UPI,
            'Cheque' => AdminExpense::CHEQUE,
        ];

        $stats = collect($expenseModes)->mapWithKeys(function ($mode, $key) use ($is_admin_expense, $company_id, $date) {
            return [
                $key => AdminExpense::whereHas('payment', function ($q) use ($mode, $is_admin_expense, $company_id, $date) {
                    $q->whereDate('created_at', $date);
                    $q->where('payment_mode', $mode);
                    $q->where('is_admin_expense', $is_admin_expense);
                    $q->where('company_id', $company_id);
                })->sum('total'),
            ];
        })->toArray();

        $stats['Total'] = AdminExpense::whereHas('payment', function ($q) use ($is_admin_expense) {
            $q->where('payment_mode', '!=', AdminExpense::CITYLEDGER)
                ->where('is_admin_expense', $is_admin_expense);
        })->sum('total');

        return $stats;
    }


    private function calculateBookingsBySource($request)
    {
        $totalCount  = Room::where('company_id', $request->company_id)->whereNot("status", Room::Blocked)->count();

        $model = Booking::query();

        $model->where('booking_status', '=', 1);

        $model->where('company_id', $request->company_id);

        $data = $model->get();

        // Step 1: Count occurrences of each type
        $countResult = [];
        foreach ($data as $item) {
            $type = $item['type'];
            if (!isset($countResult[$type])) {
                $countResult[$type] = 0;
            }
            $countResult[$type]++;
        }

        // background:rgb(204, 204, 204) // rest
        // str_pad($countResult['Walking'], 2, '0', STR_PAD_LEFT)

        $stats = [
            [
                'icon' => 'mdi-walk',
                'value' => $countResult['Walking'] ?? 0,
                'percentage' => isset($countResult['Walking'])
                    ? number_format($countResult['Walking'] / $totalCount * 100, 2)  . "%"
                    : '0%',
                'label' => 'Walk',
                'col' => 7,
                'color' => 'green',
            ],
            [
                'icon' => 'mdi-laptop',
                'value' => $countResult['Online'] ?? 0,
                'percentage' => isset($countResult['Online'])
                    ? number_format($countResult['Online'] / $totalCount * 100, 2)  . "%"
                    : '0%',
                'label' => 'OTA',
                'col' => 7,
                'color' => 'rgb(0, 217, 255)',
            ],
            [
                'icon' => 'mdi-account-tie',
                'value' => $countResult['Corporate'] ?? 0,
                'percentage' => isset($countResult['Corporate'])
                    ? number_format($countResult['Corporate'] / $totalCount * 100, 2)  . "%"
                    : '0%',
                'label' => 'Corp',
                'col' => 7,
                'color' => 'orange',
            ],
            [
                'icon' => 'mdi-cloud-outline',
                'value' => $countResult['website'] ?? 0,
                'percentage' => isset($countResult['website'])
                    ? number_format($countResult['website'] / $totalCount * 100, 2)  . "%"
                    : '0%',
                'label' => 'Web',
                'col' => 7,
                'color' => 'purple',
            ],
            [
                'icon' => 'mdi-gift-outline',
                'value' => $countResult['Complimentary'] ?? 0,
                'percentage' => isset($countResult['Complimentary'])
                    ? number_format($countResult['Complimentary'] / $totalCount * 100, 2)  . "%"
                    : '0%',
                'label' => 'Comp',
                'col' => 7,
                'color' => 'pink',
            ],
            [
                'icon' => 'mdi-account-outline',
                'value' => $countResult['Travel Agency'] ?? 0,
                'percentage' => isset($countResult['Travel Agency'])
                    ? number_format($countResult['Travel Agency'] / $totalCount * 100, 2)  . "%"
                    : '0%',
                'label' => 'Agent',
                'col' => 7,
                'color' => 'teal',
            ]
        ];

        $newTotal = abs(array_sum(array_column($stats, "value")) - $totalCount);

        $rest = number_format(($newTotal) / $totalCount * 100, 2);

        $stats[] = [
            'icon' => '---',
            'value' => "",
            'percentage' => $rest . "%",
            'label' => "Vacant $newTotal ($rest%)",
            'col' => 7,
            'color' => "#c5c5c5",
        ];

        return $stats;
    }

    function currency_format($amount)
    {
        return number_format($amount, 2);
    }
}
