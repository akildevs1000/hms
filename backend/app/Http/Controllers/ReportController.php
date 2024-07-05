<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Taxable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    public function CHeckInReport(Request $request)
    {
        $data = $this->CHeckInReportProcess($request);
        return Pdf::loadView('report.check_in', ['data' => $data, 'company' => Company::find($request->company_id)])
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
        $data = $this->CHeckOutReportDownload($request);
        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
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
            })->get();

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
        $expectCheckOutModel = BookedRoom::query();
        $data = $expectCheckOutModel->whereDate('check_out', $request->date)
            ->whereHas('booking', function ($q) use ($company_id) {
                $q->where('booking_status', '!=', 0);
                $q->where('booking_status', '=', 2);
                $q->where('company_id', $company_id);
            })->get();

        return Pdf::loadView('report.expect_check_out', ['data' => $data, 'company' => Company::find($request->company_id)])
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
        return array(
            0 =>
            array(
                'source' => 'Asian paints limited',
                'no_of_room' => 2,
                'revenue' => '5500.00',
                'percentage' => 2.68,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#1f77b4',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            1 =>
            array(
                'source' => 'Express holidays',
                'no_of_room' => 6,
                'revenue' => '18000.00',
                'percentage' => 8.76,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#ff7f0e',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            2 =>
            array(
                'source' => 'Festo india pvt ltd,chennai',
                'no_of_room' => 1,
                'revenue' => '16500.00',
                'percentage' => 8.03,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#2ca02c',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            3 =>
            array(
                'source' => 'Garudadri global voyages pvt ltd',
                'no_of_room' => 2,
                'revenue' => '6800.00',
                'percentage' => 3.31,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#d62728',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            4 =>
            array(
                'source' => 'Gulf oil lubricants india limited',
                'no_of_room' => 1,
                'revenue' => '5000.00',
                'percentage' => 2.43,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#9467bd',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            5 =>
            array(
                'source' => 'Jsw retail and distribution limited',
                'no_of_room' => 1,
                'revenue' => '2300.00',
                'percentage' => 1.12,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#8c564b',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            6 =>
            array(
                'source' => 'Mmt online',
                'no_of_room' => 2,
                'revenue' => '6763.00',
                'percentage' => 3.29,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#e377c2',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            7 =>
            array(
                'source' => 'Online - goibibo',
                'no_of_room' => 2,
                'revenue' => '4488.00',
                'percentage' => 2.18,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#7f7f7f',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            8 =>
            array(
                'source' => 'Optimus corporate travel solutions',
                'no_of_room' => 1,
                'revenue' => '2500.00',
                'percentage' => 1.22,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#bcbd22',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            9 =>
            array(
                'source' => 'Prithvi inner wears private limited',
                'no_of_room' => 1,
                'revenue' => '3000.00',
                'percentage' => 1.46,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#17becf',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            10 =>
            array(
                'source' => 'Sarto electro equipments pvt. ltd.',
                'no_of_room' => 1,
                'revenue' => '2300.00',
                'percentage' => 1.12,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#ffbb78',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            11 =>
            array(
                'source' => 'Shriram finance ltd.',
                'no_of_room' => 1,
                'revenue' => '2300.00',
                'percentage' => 1.12,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#aec7e8',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
            12 =>
            array(
                'source' => 'walking',
                'no_of_room' => 30,
                'revenue' => '130020.00',
                'percentage' => 63.28,
                'resourceId' => '',
                'title' => '',
                'background' => 'linear-gradient(135deg, #23bdb8 0, #65a986 100%)',
                'color' => '#ff9896',
                'status' => '',
                'check_in_date' => '1970-01-01 05:30',
                'check_out_date' => '1970-01-01 05:30',
            ),
        );
        $bookingModel = (new Booking)->setConnection('second_pgsql');

        $data = $bookingModel->whereCompanyId($request->company_id)
            ->whereBetween('booking_date', [$request->filter_from_date . ' 00:00:00', $request->filter_to_date . ' 23:59:59'])
            ->where('booking_status', '!=', -1)
            ->selectRaw('source, COUNT(*) as no_of_room, CAST(SUM(total_price) AS DECIMAL(10,2)) as revenue')
            ->groupBy('source')
            ->get();

        $totalSum = $data->sum('revenue'); // Total sum of total_price across all sources

        // Calculate percentage for each source
        foreach ($data as $item) {
            $percentage = ($item->revenue / $totalSum) * 100;
            $item->percentage = round($percentage, 2) . "%"; // Rounded to 2 decimal places
        }

        return $data;

        $response = Http::withoutVerifying()->get('https://backend.ezhms.com/api/get_source_rate_by_month', [
            'company_id' => 1,
            'month' => 7,
            'filter_from_date' => '2024-07-01',
            'filter_to_date' => '2024-07-31',
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Handle the response data
            $data = $response->json();
            return response()->json($data);
        } else {
            // Handle the error
            return response()->json(['error' => 'Failed to fetch data'], $response->status());
        }

        $data = Booking::whereCompanyId($request->company_id)
            //->whereMonth('check_in', $request->month)
            ->whereBetween('booking_date', [$request->filter_from_date . ' 00:00:00', $request->filter_to_date . ' 23:59:59'])
            ->where('booking_status', '!=', -1)
            ->select('source', 'total_price')
            ->get()
            ->groupBy('source')
            ->map(function ($group) {
                return $group->sum('total_price');
            });

        return $data;
    }
}
