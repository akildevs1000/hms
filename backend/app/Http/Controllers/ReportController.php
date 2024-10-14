<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Taxable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;


class ReportController extends Controller
{
    public function CHeckInReport(Request $request)
    {

        $company_id = $request->company_id;

        $data1 = BookedRoom::whereDate('check_out', $request->date)
            ->where('booking_status', 2)
            ->where('company_id', $company_id)
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get()->toArray();

        $data2 = BookedRoom::whereDate('check_in', $request->date)
            ->where('booking_status', 2)
            ->where('company_id', $company_id)
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get()->toArray();

        $data = array_merge($data1, $data2);

        return Pdf::loadView('report.check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function InHouseReport(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();

        // right now i have has Many relation with Posting. but i want sum of that postings
        // how to get postings sum 
        $data1 = $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();


        $datamodel2 = BookedRoom::query();

        $data2 = $datamodel2->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();

        $data = array_merge($data1->toArray(), $data2->toArray());

        $company = Company::find($request->company_id);

        return Pdf::loadView('report.inhouse', compact("data", "company"))
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function FoodOrderReport(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();

        // right now i have has Many relation with Posting. but i want sum of that postings
        // how to get postings sum 
        $data1 = $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();


        $datamodel2 = BookedRoom::query();

        $data2 = $datamodel2->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();

        $data = array_merge($data1->toArray(), $data2->toArray());

        $company = Company::find($request->company_id);

        return Pdf::loadView('report.foodorder', compact("data", "company"))
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function CHeckInReportDownload(Request $request)
    {
        $data = $this->CHeckInReportProcess($request);
        return Pdf::loadView('report.check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function CHeckInReportProcess($request)
    {
        $company_id = $request->company_id;
        $checkInModel = BookedRoom::query();
        return $checkInModel->clone()
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();
    }

    public function CHeckOutReport(Request $request)
    {

        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();

        // right now i have has Many relation with Posting. but i want sum of that postings
        // how to get postings sum 
        $data = $expectCheckInModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 3);
                $q->where('company_id', $company_id);
            })

            ->with(['booking' =>  function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 3);
                $q->where('company_id', $company_id);
            }])

            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();

        $company = Company::find($request->company_id);

        return Pdf::loadView('report.check_out', compact("data", "company"))
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function CHeckOutReportDownload(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data = $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function CHeckOutReportProcess($request)
    {
        $company_id = $request->company_id;
        $checkOutModel = BookedRoom::query();
        return $checkOutModel->clone()->whereDate('check_out', $request->check_in)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 3);
                $q->where('company_id', $company_id);
            })->get();
    }

    public function availableRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        $model = BookedRoom::query();
        $roomIds = $model
            ->whereDate('check_in', '<=', $request->date)
            ->whereHas('booking', function ($q) use ($company_id, $request) {
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '<=', 4);
                $q->where('company_id', $company_id);
                $q->whereDate('check_in', '<=', $request->date);
            })
            ->with('booking')
            ->pluck('room_id');
        $data = Room::whereNotIn('id', $roomIds)->where('company_id', $company_id)->get();
        return $data;
    }

    public function availableRoomsReport(Request $request)
    {
        $data = $this->availableRoomsReportProcess($request);
        return Pdf::loadView('report.available_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function availableRoomsReportDownload(Request $request)
    {
        $data = $this->availableRoomsReportProcess($request);
        return Pdf::loadView('report.available_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function bookedRoomsReport(Request $request)
    {
        $data = $this->bookedRoomsReportProcess($request);

        return Pdf::loadView('report.booked_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function bookedRoomsReportDownload(Request $request)
    {
        $data = $this->bookedRoomsReportProcess($request);
        return Pdf::loadView('report.booked_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function bookedRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        $reservedWithoutAdvanceModel = BookedRoom::query();
        return $reservedWithoutAdvance = $reservedWithoutAdvanceModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', -1);
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 1);
                $q->where('advance_price', '=', 0); //new line
                $q->where('company_id', $company_id);
            })->get();
    }

    public function paidRoomsReport(Request $request)
    {
        $data = $this->paidRoomsReportProcess($request);
        return Pdf::loadView('report.paid_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function paidRoomsReportDownload(Request $request)
    {
        $data = $this->paidRoomsReportProcess($request);
        return Pdf::loadView('report.paid_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function paidRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        return BookedRoom::whereHas('booking', function ($q) use ($company_id) {
            $q->where('booking_status', '!=', 0);
            $q->where('booking_status', 1);
            $q->where('advance_price', '!=', 0);
            $q->where('company_id', $company_id);
        })->get();
    }

    public function dirtyRoomsReport(Request $request)
    {
        $data = $this->dirtyRoomsReportProcess($request);
        return Pdf::loadView('report.dirty_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function dirtyRoomsReportDownload(Request $request)
    {
        $data = $this->dirtyRoomsReportProcess($request);
        return Pdf::loadView('report.dirty_rooms', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function dirtyRoomsReportProcess($request)
    {
        $company_id = $request->company_id;
        // return $dirtyRooms = BookedRoom::whereHas('booking', function ($q) use ($company_id) {
        //         $q->where('booking_status', '!=', 0);
        //         $q->where('booking_status', 3);
        //         $q->where('company_id', $company_id);
        //     })->get();

        return $dirtyRooms = BookedRoom::where('booking_status', '!=', 0)
            ->where('booking_status', 3)
            ->where('company_id', $company_id)
            ->get();

        // ------------------backup------------------------------
        // $company_id        = $request->company_id;
        // return $dirtyRooms = BookedRoom::whereHas('booking', function ($q) use ($company_id) {
        //     $q->where('booking_status', '!=', 0);
        //     $q->where('booking_status', 3);
        //     $q->where('company_id', $company_id);
        // })->get();
        // ------------------------------------------------

    }

    // -----------------------

    public function expectCHeckInReport(Request $request)
    {

        $company_id = $request->company_id;

        $data = BookedRoom::whereDate('check_in', '<=', $request->date)
            ->whereDate('check_out', '>=', $request->date)
            ->where('booking_status', 1)
            ->where('company_id', $company_id)
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get()->toArray();

        return Pdf::loadView('report.expect_check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function expectCHeckInReportDownload(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();
        $data = $expectCheckInModel->whereDate('check_in', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 1);
                $q->where('advance_price', '>', 0); //new line
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function expectCHeckOutReport(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckInModel = BookedRoom::query();

        // right now i have has Many relation with Posting. but i want sum of that postings
        // how to get postings sum 
        $data1 = $expectCheckInModel->whereDate('check_in', $request->date)
            ->where('booking_status', '!=', 0)
            ->where('booking_status', '=', 2)
            ->where('company_id', $company_id)
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();


        $datamodel2 = BookedRoom::query();

        $data2 = $datamodel2->whereDate('check_out', $request->date)
            ->where('booking_status', '!=', 0)
            ->where('booking_status', '=', 2)
            ->where('company_id', $company_id)
            ->withSum('postings', 'amount_with_tax')
            ->orderBy("updated_at", "desc")
            ->get();

        $data = array_merge($data1->toArray(), $data2->toArray());

        $company = Company::find($request->company_id);

        return Pdf::loadView('report.expect_check_out', compact("data", "company"))
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function expectCHeckOutReportDownload(Request $request)
    {
        $company_id = $request->company_id;
        $expectCheckOutModel = BookedRoom::query();
        $data = $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }

    public function gstInvoiceReportProcess($request)
    {

        $taxable = new TaxableController();
        $model = $taxable->getTaxableProcess($request);

        return  $model->get();

        // $model = Taxable::query();

        // $model->whereCompanyId($request->company_id)
        //     ->whereHas('booking', function ($q) {
        //         $q->where('booking_status', '!=', -1);
        //     });

        // if (($request->filled('search') && $request->search)) {
        //     $model->whereHas('booking', function ($q) use ($request) {
        //         $q->where('gst_number', 'Like', '%' . $request->search . '%');
        //         $q->orWhere('reservation_no', 'Like', '%' . $request->search . '%');
        //     });
        // }

        // if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
        //     $model->whereHas('booking', function ($q) use ($request) {
        //         $q->WhereDate('check_in', '>=', $request->from);
        //         $q->whereDate('check_in', '<=', $request->to);
        //     });
        // }

        // if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
        //     $model->whereHas('booking', function ($q) use ($request) {
        //         $q->WhereDate('check_out', '>=', $request->from);
        //         $q->whereDate('check_out', '<=', $request->to);
        //     });
        // }

        // $model->with('booking.customer');
        // $model->orderBy('id', 'asc');

        // return $model->get();
    }

    public function gstInvoiceReport(Request $request)
    {
        $data = $this->gstInvoiceReportProcess($request);
        return Pdf::loadView('report.gst_invoice', ['data' => $data, 'request' => $request, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'landscape')
            ->stream();
    }

    public function gstInvoiceReportDownload(Request $request)
    {
        $data = $this->gstInvoiceReportProcess($request);
        return Pdf::loadView('report.gst_invoice', ['data' => $data, 'request' => $request, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'landscape')
            ->download();
    }

    public function gstInvoiceReportCSVDownload(Request $request)
    {
        $data = $this->gstInvoiceReportProcess($request);



        $columns = array('#', 'Invoice Number', 'Resr.No', 'GST', 'Customer', 'Check-in', 'Check-out', 'Inv Amount ', 'Tax Collected', 'Total');
        $csvDataRowArr = [];
        $counter = 1;
        $inv_grand_total_amount = 0;
        $inv_grand_tax_total_amount = 0;
        foreach ($data as  $bookingObj) {
            $invTotal = $bookingObj->booking->inv_total_tax_collected + $bookingObj->booking->inv_total_without_tax_collected;

            $rowData = [

                $counter++,
                $bookingObj->show_taxable_invoice_number,
                $bookingObj->reservation_no,
                $bookingObj->booking->customer->gst_number,
                $bookingObj->booking->customer->full_name,

                $bookingObj->booking->check_in,
                $bookingObj->booking->check_out,
                $bookingObj->booking->inv_total_without_tax_collected,

                $bookingObj->booking->inv_total_tax_collected,
                $invTotal,



            ];
            $csvDataRowArr[] = $rowData;
            $inv_grand_total_amount += $bookingObj->booking->inv_total_without_tax_collected;
            $inv_grand_tax_total_amount += $bookingObj->booking->inv_total_tax_collected;
        }

        $rowData = [

            '',
            '',
            '',
            '',
            '',
            '',
            'Total',

            $inv_grand_total_amount,
            $inv_grand_tax_total_amount,
            $inv_grand_tax_total_amount + $inv_grand_total_amount,

        ];
        $csvDataRowArr[] = $rowData;



        return $this->exportCsv($csvDataRowArr, $columns, 'GST Report ' . $request->from . '-' . $request->to . '.csv');
    }

    public function incomingReportProcess($request)
    {
        // ini_set('memory_limit', '1024M');
        $model = Payment::query();
        $model->where('company_id', $request->company_id);
        $model->whereHas('booking', function ($q) {
            $q->where('booking_status', '!=', -1);
        });
        $model->with("booking:id,reservation_no");
        $model->orderByDesc("id");
        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->from;
            $to = $request->to;
            $model->whereDate('created_at', '>=', $from);
            $model->whereDate('created_at', '<=', $to);
        }
        $totalIncomes = $this->TransactionsCounts($request);
        return Pdf::loadView('report.income', [
            'data' => $model->get(),
            'request' => $request,
            'totalIncomes' => $totalIncomes['income'],
            'accounts' => $totalIncomes,
            'company' => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function incomingReport(Request $request)
    {
        return $this->incomingReportProcess($request)->stream();
    }

    public function incomingReportDownload(Request $request)
    {
        return $this->incomingReportProcess($request)->download();
    }

    public function expenseReportProcess($request)
    {
        $model = Expense::query();
        $model->where('company_id', $request->company_id);
        $model->orderBy("id", 'asc');
        $type = "";

        // if ($request->filled('from') && $request->filled('to')) {
        //     $from = $request->from;
        //     $to   = $request->to;
        //     $model->whereDate('created_at', '>=', $from);
        //     $model->whereDate('created_at', '<=', $to);
        // }

        if ($request->filled('from') && $request->filled('to')) {
            $model->whereDate('created_at', '>=', $request->from);
            $model->whereDate('created_at', '<=', $request->to);
            $model->orderBy("created_at", 'asc');
        } else {
            $model->orderBy("created_at", 'desc');
        }

        if ($request->is_management == 1 && $request->has('is_management') && $request->filled('is_management')) {
            $model->where('is_management', 1);
            $type = "Management";
        } else {
            $model->where('is_management', 0);
        }

        $totalExpenses = $this->TransactionsCounts($request);
        return Pdf::loadView('report.expense', [
            'data' => $model->get(),
            'request' => $request,
            'totalExpenses' => $totalExpenses['expense'],
            'accounts' => $totalExpenses,
            'type' => $type,
            'company' => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function expenseReport(Request $request)
    {
        // return $this->expenseReportProcess($request);
        return $this->expenseReportProcess($request)->stream();
    }

    public function expenseReportDownload(Request $request)
    {
        return $this->expenseReportProcess($request)->download();
    }

    public function reservationReportProcess($request)
    {
        $status = $request->r_type;
        $reservationTYpe = "";
        $model = Booking::query()->latest()->filter(request('search'));

        if ($request->guest_mode == 'Arrival' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_in', '>=', $request->from);
            $model->whereDate('check_in', '<=', $request->to);
        }

        if ($request->guest_mode == 'Departure' && ($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('check_out', '>=', $request->from);
            $model->whereDate('check_out', '<=', $request->to);
        }

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', $request->source);
        }

        $model->orderBy('id', 'desc');

        switch ($status) {
            case 'up_coming_reservation_list':
                $model->where('booking_status', '=', 1);
                $reservationTYpe = 'UpComing';
                break;
            case 'check_out_reservation_list':
                $model->where(function ($q) {
                    $q->where('booking_status', '=', 3);
                    $q->orWhere('booking_status', '=', 0);
                });
                $reservationTYpe = 'Checkout';
                break;
            case 'in_house_reservation_list':
                $model->where('booking_status', '=', 2);
                $reservationTYpe = 'In House';
                break;
            case 'hall/upcoming_reservation_list':
                $model->where('booking_status', '=', 1);
                $model->where('room_category_type', '=', 'Hall');
                $reservationTYpe = 'Upcoming';
                break;
            case 'hall/in_house_reservation_list':
                $model->where('booking_status', '=', 2);
                $model->where('room_category_type', '=', 'Hall');
                $reservationTYpe = 'In House';
                break;
            case 'hall/check_out_reservation_list':
                $model->where(function ($q) {
                    $q->where('booking_status', '=', 3);
                    $q->orWhere('booking_status', '=', 0);
                });
                $model->where('room_category_type', '=', 'Hall');
                $reservationTYpe = 'Checkout';
                break;
            default:
                abort(400, 'Invalid status');
        }

        $data = $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type',
                'customer:id,first_name,last_name,document',
            ])
            ->where('company_id', $request->company_id)
            ->get();

        return Pdf::loadView('report.reservation_list', [
            'data' => $data,
            'reservationTYpe' => $reservationTYpe,
            'request' => $request,
            'company' => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function reservationReport(Request $request)
    {
        // return $this->reservationReportProcess($request);
        return $this->reservationReportProcess($request)->stream();
    }

    public function reservationReportDownload(Request $request)
    {
        return $this->reservationReportProcess($request)->download();
    }

    public function revenueMonthlyReportPrint(Request $request)
    {
        return $this->revenueMonthlyReport($request)->stream();
    }
    public function revenueMonthlyReportDownload(Request $request)
    {
        return $this->revenueMonthlyReport($request)->download();
    }
    public function revenueDailyReportPrint(Request $request)
    {
        return $this->revenueDailyReport($request)->stream();
    }
    public function revenueDailyReportDownload(Request $request)
    {
        return $this->revenueDailyReport($request)->download();
    }
    public function revenueCustomerWiseReportPrint(Request $request)
    {
        return $this->revenueCustomerReport($request)->stream();
    }
    public function revenueCustomerWiseReportDownload(Request $request)
    {
        return $this->revenueCustomerReport($request)->download();
    }

    public function revenueMonthlyReport(Request $request)
    {
        $object = new ManagementController();
        $data = $object->getReportMonthlyWiseGroup($request);

        return Pdf::loadView('report.revenue_report_monthlywise', [
            'data' => $data,

            'request' => $request,
            'company' => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }
    public function revenueDailyReport(Request $request)
    {
        $object = new ManagementController();
        $data = $object->getReportDailyWiseGroup($request);

        return Pdf::loadView('report.revenue_report_daywise', [
            'data' => $data,

            'request' => $request,
            'company' => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }
    public function revenueCustomerReport(Request $request)
    {
        $object = new ManagementController();
        $data = $object->getReportTop10Customers($request);

        return Pdf::loadView('report.revenue_report_customer_wise', [
            'data' => $data,

            'request' => $request,
            'company' => Company::find($request->company_id),
        ])->setPaper('a4', 'landscape');
    }

    public function exportCsv($data, $columns, $fileName)
    {
        //$fileName = 'tasks.csv';
        //$tasks = Task::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );



        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $task) {


                fputcsv($file, $task);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function reportBySource(Request $request)
    {

        // $bookingModel = (new Booking)->setConnection('second_pgsql');

        $bookingModel = (new Booking);

        $from = date("Y-m-d 00:00:00");
        $to = date("Y-m-d 23:59:59");

        if (request()->filled("filter_from_date")) {
            $from = $request->filter_from_date . ' 00:00:00';
        }
        if (request()->filled("filter_to_date")) {
            $to = $request->filter_to_date . ' 23:59:59';
        }

        $data = $bookingModel->whereCompanyId(1)
            ->whereBetween('booking_date', [$from, $to])
            ->where('booking_status', '!=', -1)
            ->selectRaw('source, COUNT(*) as no_of_room, CAST(SUM(total_price) AS DECIMAL(10,2)) as revenue')
            ->groupBy('source')
            ->get();

        $totalSum = $data->sum('revenue'); // Total sum of total_price across all sources

        $colors = ["#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf", "#ffbb78", "#aec7e8", "#ff9896"];

        $newData = [];

        foreach ($data as $index => $item) {

            $percentage = ($item['revenue'] / $totalSum) * 100;
            $item['percentage'] =  // Rounded to 2 decimal places
                $colorIndex = $index % count($colors);

            $newData[] = [
                "source" => $item["source"],
                "no_of_room" => $item["no_of_room"],
                "revenue" => $item["revenue"],
                "percentage" => round($percentage, 2) . "%",
                "color" => $colors[$colorIndex],
            ];
        }


        return [
            "colors" => $colors,
            "data" => $newData,
        ];

        // $response = Http::withoutVerifying()->get('https://backend.ezhms.com/api/get_source_rate_by_month', [
        //     'company_id' => 1,
        //     'month' => 7,
        //     'filter_from_date' => '2024-07-01',
        //     'filter_to_date' => '2024-07-31',
        // ]);

        // // Check if the request was successful
        // if ($response->successful()) {
        //     // Handle the response data
        //     $data = $response->json();
        //     return response()->json($data);
        // } else {
        //     // Handle the error
        //     return response()->json(['error' => 'Failed to fetch data'], $response->status());
        // }
    }

    public function getReportTopTenCustomers(Request $request)
    {
        $from = date("Y-m-d 00:00:00");
        $to = date("Y-m-d 23:59:59");

        if (request()->filled("filter_from_date")) {
            $from = $request->filter_from_date . ' 00:00:00';
        }
        if (request()->filled("filter_to_date")) {
            $to = $request->filter_to_date . ' 23:59:59';
        }

        // $bookingModel = (new Booking)->setConnection('second_pgsql');

        $bookingModel = (new Booking);

        $data = $bookingModel->select(
            DB::raw("string_agg(rooms, ',') as rooms"),
            'bookings.customer_id',
            DB::raw('sum(total_price) as revenue'),
            DB::raw('count(id) as number_of_visits'),
        )
            ->with('customer:id,contact_no,first_name,last_name')
            ->groupBy('bookings.customer_id')
            ->orderByDesc('revenue')
            ->where('company_id', 1)
            ->where('booking_status', "!=", -1)
            ->where(function ($query) use ($from, $to) {
                $query->where(function ($query) use ($from, $to) {
                    $query->where('check_in', '>=', $from)
                        ->where('check_in', '<=', $to);
                });
                $query->orWhere(function ($query) use ($from, $to) {
                    $query->where('check_in', '<=', $from)
                        ->where('check_out', '>=', $to);
                });
            })
            ->take(10)
            ->get();

        $totalSum = $data->sum('revenue'); // Total sum of total_price across all sources

        $colors = ["#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf", "#ffbb78", "#aec7e8", "#ff9896"];

        $newData = [];

        foreach ($data as $index => $item) {

            $percentage = ($item['revenue'] / $totalSum) * 100;

            $item['percentage'] =  // Rounded to 2 decimal places

                $colorIndex = $index % count($colors);


            $newData[] = [
                "source" => $item["customer"]->full_name ?? "",
                "revenue" => $item["revenue"],
                "percentage" => round($percentage, 2) . "%",
                "color" => $colors[$colorIndex],
                "no_of_room" => count(explode(',', $item['rooms'] ?? 0)),
                "number_of_visits" => $item['number_of_visits'],
            ];
        }

        return [
            "colors" => $colors,
            "data" => $newData,
        ];
    }

    public function htmlTest(Request $request)
    {
        $company_id = $request->company_id ?? 3;

        return Pdf::loadView('report.html.test', ['data' => [], 'company' => Company::find($company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function nightAuditSummary()
    {
        $company_id = 3;
        $date = date("Y-m-d");
        $fileName = "summary";

        return (new ReportGenerateController)->processSummaryData($company_id, $date);

        $pdf = PDF::loadView("report.audit.summary")->stream();
        $file_path  = "pdf/" . $date . '/' . $company_id . '/' . $fileName . '.pdf';
        Storage::disk('local')->put($file_path, $pdf);
        return $pdf;
    }



    // public function mergePdfFiles(array $pdfFiles, $outputPath)
    // {
    //     // Initialize FPDI
    //     $pdf = new FPDI();

    //     // Loop through each PDF file
    //     foreach ($pdfFiles as $file) {
    //         $pageCount = $pdf->setSourceFile($file);

    //         // Add each page from the source PDF to the final output
    //         for ($i = 1; $i <= $pageCount; $i++) {
    //             $tplId = $pdf->importPage($i);
    //             $pdf->AddPage();
    //             $pdf->useTemplate($tplId);
    //         }
    //     }

    //     // Output the merged PDF (you can save it to a file or download it)
    //     $pdf->Output($outputPath, 'F');  // 'F' for saving to file, 'I' for inline, 'D' for download

    //     return response()->download($outputPath);
    // }
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
    public function downloadMergedPdf()
    {
        $company_id = 3;
        $date = date("Y-m-d");


        $pdfFiles = [
            storage_path("app/pdf/$date/$company_id/summary.pdf"),
            storage_path("app/pdf/$date/$company_id/Today Check-in Report.pdf"),
            storage_path("app/pdf/$date/$company_id/Continue Report.pdf"),
            storage_path("app/pdf/$date/$company_id/Check-out Report.pdf"),
            storage_path("app/pdf/$date/$company_id/Today Booking Report.pdf"),
            storage_path("app/pdf/$date/$company_id/Cancel Rooms Report.pdf"),
            storage_path("app/pdf/$date/$company_id/City Ledger Report.pdf"),
            storage_path("app/pdf/$date/$company_id/Food Order list.pdf"),
        ];

        $outputPath = storage_path("app/pdf/$date/$company_id/merged_file.pdf");

        $this->mergePdfFiles($pdfFiles, $outputPath);

        return response()->json(['message' => 'PDF merged and stored successfully.', 'path' => $outputPath]);
    }
}
