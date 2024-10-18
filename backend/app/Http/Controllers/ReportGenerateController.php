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
            [
                "label" => "Checkin",
                "value" => $this->currency($todayCheckin->count()),
                "color" => "#ffc000",
            ],
            [
                "label" => "Continue",
                "value" => $this->currency($continueRooms->count()),
                "color" => "#03c1ec",
            ],
            [
                "label" => "Day Use",
                "value" => $this->currency($todayCheckOut->count()),
                "color" => "#71de36",
            ],
            [
                "label" => "Complementary",
                "value" => $this->currency($todayCheckOut->count()),
                "color" => "#800080",
            ],
            [
                "label" => "Checkout",
                "value" => $this->currency($todayCheckOut->count()),
                "color" => "#dc3545",
            ],
            [
                "label" => "Closing",
                "value" => $this->currency(0),
                "color" => "#a6a6a6",
            ],
        ];
        $foodOrders = $this->processFoodOrderList($this->foodAudit($request)->get()->toArray());

        $foodOrdersArray = [
            [
                "label" => "Breakfast",
                "value" => $foodOrders->breakfast ?? 0,
                "color" => "green",
            ],
            [
                "label" => "Lunch",
                "value" => $foodOrders->lunch ?? 0,
                "color" => "purple",
            ],
            [
                "label" => "Dinner",
                "value" => $foodOrders->dinner ?? 0,
                "color" => "orange",
            ],
        ];


        $summary = [
            'room' => $guestArray,
            'guest' => $foodOrdersArray,
            'occupied' => $this->getOccupied($request)->count(),
            'income' => $this->getIncome($request),
            'expense' => $this->getExpense($request),
            'managementExpense' => $this->getExpense($request, AdminExpense::ManagementExpense),
            "calculateBookingsBySource" => $this->calculateBookingsBySource($request),
        ];

        $arr = [];

        $CashIncome = $this->getValueFromList($summary["income"], "Cash");
        $TotalIncome = $this->getValueFromList($summary["income"]);

        $CashExpense = $this->getValueFromList($summary["expense"], "Cash");
        $TotalExpense = ($this->getValueFromList($summary["expense"]) ?? 0) + ($this->getValueFromList($summary["managementExpense"]) ?? 0);

        $OtherIncome = $TotalIncome - $CashIncome;
        $OtherExpense = $TotalExpense - $CashExpense;
        $CashInHand = $CashIncome - $CashExpense;
        $finalStatement = $TotalIncome - $TotalExpense;



        $summary["profit_loss"] = [
            [
                "label" => "Loss",
                "value" => $this->currency($finalStatement > 0 ? 0 : $finalStatement),
                "color" => "red"
            ],
            [
                "label" => "Profit",
                "value" => $this->currency($finalStatement < 0 ? 0 : $finalStatement),
                "color" => "green"
            ],
            [
                "label" => "City Ledger",
                "value" => $this->getValueFromList($summary["income"], "City Ledger"),
                "color" => "orange"
            ]
        ];


        $summary["balance_sheet"] = [
            "labels" => [
                "Income (Cash)",
                "Income (Other)",
                "Expense (Cash)",
                "Expense (Other)",
                ""
            ],

            "values" => [
                ["value" => $this->currency($CashIncome)],
                ["value" => $this->currency($OtherIncome)],
                ["value" => $this->currency($CashExpense)],
                ["value" => $this->currency($OtherExpense)],
                [
                    "value" => "Cash in Hand (" . $this->currency($CashInHand) . ")",
                    "color" => "green"
                ],
            ],

            "totals" => [
                ["value" => $this->currency($TotalIncome), "colspan" => 2],
                ["value" => $this->currency($TotalExpense), "colspan" => 2],
                [
                    "value" => "Profit/Loss (" . $this->currency($finalStatement) . ")",
                    "color" => $finalStatement < 0 ? "red" : "green"
                ],
            ],
        ];

        // return $summary;

        $this->processPayload("summary", "Summary", $date, $company_id, $summary, "summary");

        $this->processPayload("check_in", "Today Check-in Report", $date, $company_id, $todayCheckin->get(), "today_check_in");
        $this->processPayload("continue", "Continue Report", $date, $company_id, $continueRooms->get(), "continue_report");
        $this->processPayload("check_out", "Check-out Report", $date, $company_id, $todayCheckOut->get(), "check_out_report");
        $this->processPayload("payment", "Today Booking Report", $date, $company_id, $todayPayments->get(), "today_booking_report");
        $this->processPayload("cityLedger", "City Ledger Report", $date, $company_id, $cityLedgerPaymentsAudit->get(), "city_ledger_report");
        $this->processPayload("cancel", "Cancel Rooms Report", $date, $company_id, $cancelRooms->get(), "cancel_rooms");
        $this->processPayload("food", "Food Order list", $date, $company_id, $this->foodAudit($request)->get(), "food_order_list");

        $arr = [
            "type" => "expense",
            "file_name" => "---",
            "file_path" => "---",
            'data' => $this->currency($TotalExpense),
            'company_id' => $company_id,
            'dateTime' => date("d M y h:i:s"),
        ];
        AuditHistory::where("company_id", $company_id)->whereDate("created_at", $date)->delete();
        AuditHistory::create($arr);

        return $this->mergeAndGeneratePDFFiles($company_id, $date);
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
        // Mapping payment types to their constants
        $modes = [
            'Cash' => AdminExpense::CASH,
            'Card' => AdminExpense::CARD,
            'Online' => AdminExpense::ONLINE,
            'Bank' => AdminExpense::BANK,
            'UPI' => AdminExpense::UPI,
            'Cheque' => AdminExpense::CHEQUE,
            'City Ledger' => AdminExpense::CITYLEDGER,
        ];

        // Initialize stats for each mode
        $stats = array_fill_keys($modes, 0);

        // Fetch payments for the given company and date
        $data = Payment::query()
            ->where('company_id', $request->company_id)
            ->whereDate('date', $request->date)
            ->get();

        foreach ($data as $item) {
            $payment_type = $item->payment_type;
            $paymentTypeName = $payment_type->name ?? null;

            if ($paymentTypeName && array_key_exists($paymentTypeName, $modes)) {
                // Accumulate the amount for the respective payment type
                $stats[$paymentTypeName] += $item->amount;
            }
        }

        return [
            [
                "label" => AdminExpense::CASH,
                "value" => $this->currency($stats[AdminExpense::CASH] ?? 0),
                "color" => "green",
            ],
            [
                "label" => AdminExpense::CARD,
                "value" => $this->currency($stats[AdminExpense::CARD] ?? 0),
                "color" => "purple",
            ],
            [
                "label" => AdminExpense::ONLINE,
                "value" => $this->currency($stats[AdminExpense::ONLINE] ?? 0),
                "color" => "orange",
            ],
            [
                "label" => AdminExpense::BANK,
                "value" => $this->currency($stats[AdminExpense::BANK] ?? 0),
                "color" => "red",
            ],
            [
                "label" => AdminExpense::UPI,
                "value" => $this->currency($stats[AdminExpense::UPI] ?? 0),
                "color" => "teal",
            ],
            [
                "label" => AdminExpense::CHEQUE,
                "value" => $this->currency($stats[AdminExpense::CHEQUE] ?? 0),
                "color" => "blue",
            ],
            [
                "label" => "Total",
                "value" => $this->currency(array_sum($stats) - $stats['City Ledger'] ?? 0),
                "color" => "grey",
            ],
            [
                "label" => "City Ledger",
                "value" => $stats['City Ledger'] ?? 0,
                "color" => "grey",
                "hide" => true
            ],
        ];
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

        $stats['Total'] = AdminExpense::whereHas('payment', function ($q) use ($is_admin_expense, $date) {
            $q->whereDate('created_at', $date);
            $q->where('payment_mode', '!=', AdminExpense::CITYLEDGER)
                ->where('is_admin_expense', $is_admin_expense);
        })->sum('total');

        return [
            [
                "label" => AdminExpense::CASH,
                "value" => $this->currency($stats[AdminExpense::CASH] ?? 0),
                "color" => "green",
            ],
            [
                "label" => AdminExpense::CARD,
                "value" => $this->currency($stats[AdminExpense::CARD] ?? 0),
                "color" => "purple",
            ],
            [
                "label" => AdminExpense::ONLINE,
                "value" => $this->currency($stats[AdminExpense::ONLINE] ?? 0),
                "color" => "orange",
            ],
            [
                "label" => AdminExpense::BANK,
                "value" => $this->currency($stats[AdminExpense::BANK] ?? 0),
                "color" => "red",
            ],
            [
                "label" => AdminExpense::UPI,
                "value" => $this->currency($stats[AdminExpense::UPI] ?? 0),
                "color" => "teal",
            ],
            [
                "label" => AdminExpense::CHEQUE,
                "value" => $this->currency($stats[AdminExpense::CHEQUE] ?? 0),
                "color" => "blue",
            ],
            [
                "label" => "Total",
                "value" => $this->currency($stats["Total"] ?? 0),
                "color" => "grey",
            ],
        ];
    }


    private function calculateBookingsBySource($request)
    {
        $totalCount  = Room::where('company_id', $request->company_id)->whereNot("status", Room::Blocked)->count();

        $model = Booking::query();

        $model->where('booking_status', '=', 1);

        $model->where('company_id', $request->company_id);

        $model->whereDate('booking_date', date("Y-m-d"));

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

    function currency($amount)
    {
        return (int) $amount;
        return number_format($amount, 2);
    }

    public function getValueFromList($data, $column = "Total")
    {
        $totalItem = array_filter($data, fn($item) => $item['label'] === $column);

        $totalValue = reset($totalItem)['value'];

        return (int)$totalValue;
    }

    public function mergeAndGeneratePDFFiles($company_id, $date)
    {
        try {
            $pdfFiles = [
                storage_path("app/pdf/$date/$company_id/Summary.pdf"),
                storage_path("app/pdf/$date/$company_id/Today Check-in Report.pdf"),
                storage_path("app/pdf/$date/$company_id/Continue Report.pdf"),
                storage_path("app/pdf/$date/$company_id/Check-out Report.pdf"),
                storage_path("app/pdf/$date/$company_id/Today Booking Report.pdf"),
                storage_path("app/pdf/$date/$company_id/Cancel Rooms Report.pdf"),
                storage_path("app/pdf/$date/$company_id/City Ledger Report.pdf"),
                storage_path("app/pdf/$date/$company_id/Food Order list.pdf"),
            ];

            $outputDir = storage_path("app/pdf/$date/$company_id");
            $outputPath = storage_path("app/pdf/$date/$company_id/merged_file.pdf");

            // Create the directory if it doesn't exist
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 777, true);  // Create the directory recursively
            }

            $this->mergePdfFiles($pdfFiles, $outputPath);

            return response()->json(['message' => 'PDF merged and stored successfully.', 'path' => $outputPath]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mergePdfFiles(array $pdfFiles, $outputPath)
    {
        // Initialize FPDI
        $pdf = new \setasign\Fpdi\Fpdi();

        // Loop through each PDF file
        foreach ($pdfFiles as $file) {
            $pageCount = $pdf->setSourceFile($file);

            // Add each page from the source PDF to the final output
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplId);  // Get the page size of the imported PDF

                // Adjust orientation based on the original page's width and height
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';  // Auto-detect orientation

                // Add a new page with the detected orientation
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }
        }
        // Save the merged PDF to the specified output path
        $pdf->Output($outputPath, 'F');  // 'F' for saving to file

        return $outputPath;  // Return the path to the saved file
        // Stream the merged PDF directly to the browser
        return response($pdf->Output($outputFileName, 'I'))->header('Content-Type', 'application/pdf');
    }
}
