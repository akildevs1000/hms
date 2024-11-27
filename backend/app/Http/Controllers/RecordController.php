<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Record;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RecordController extends Controller
{
    public function summaryReport()
    {
        $headers = [
            ["align" => "center", "text" =>  "Date", "value" => "date"],
            ["align" => "center", "text" =>  "Room Sold", "value" => "sold"],
            ["align" => "center", "text" =>  "Cash", "value" => "cash"],
            ["align" => "center", "text" =>  "Card", "value" => "card"],
            ["align" => "center", "text" =>  "Online", "value" => "online"],
            ["align" => "center", "text" =>  "Bank", "value" => "bank"],
            ["align" => "center", "text" =>  "UPI", "value" => "upi"],
            ["align" => "center", "text" =>  "cheque", "value" => "cheque"],
            ["align" => "center", "text" =>  "Ledger", "value" => "ledger"],
            ["align" => "center", "text" =>  "Total", "value" => "total"],
        ];

        $countableFields = [
            "sold",
            "cash",
            "card",
            "online",
            "bank",
            "upi",
            "cheque",
            "ledger",
            "total",
        ];

        return $this->getReport($headers, $countableFields, Record::SUMMARY);
    }

    public function cashReport()
    {
        $headers = [
            ["align" => "center", "text" =>  "Date", "value" => "date"],
            ["align" => "center", "text" =>  "Room Sold", "value" => "sold"],
            ["align" => "center", "text" =>  "Cash", "value" => "cash"],
            ["align" => "center", "text" =>  "Expense", "value" => "expense"],
            ["align" => "center", "text" =>  "Balance", "value" => "balance"],
        ];

        $countableFields = [
            "sold",
            "cash",
            "expense",
            "balance"
        ];

        return $this->getReport($headers, $countableFields, Record::CASH);
    }

    public function otaTRN()
    {
        $sources_list = Source::get(["name", "short_name"])->toArray();

        $headers = array_map(function ($item) {
            return [
                "text" => $item["short_name"],
                "align" => "right",
                "sortable" => true,
                "filterable" => false,
                "value" => $item["name"],
            ];
        }, $sources_list);

        $finalHeaders = array_merge([
            [
                "text" => "Date",
                "align" => "left",
                "sortable" => true,
                "filterable" => false,
                "value" => "date",
            ],
            // ["Cash", "Bank", "UPI", "Pending"]
        ], $headers);

        $countableFields = [];

        return $this->getReport($finalHeaders, $countableFields, Record::OTA);
    }

    public function getReport($headers, $countableFields, $type)
    {
        $result = Record::query()
            ->where("type", $type)
            ->where('company_id', request("company_id"))
            ->whereBetween('date', [request("from_date"), request("to_date")])
            ->orderBy("date", "asc")
            ->paginate(request("per_page", 500));

        return [
            "headers" => $headers,
            "countableFields" => $countableFields,
            "data" => $result->items(),
            "summaryRow" => $this->getSummaryRow($countableFields, $result->items())
        ];
    }

    public function getSummaryRow($countableFields, $items)
    {
        return collect($countableFields)->reduce(function ($acc, $field) use ($items) {
            // Calculate the sum of the field from the nested "oldValues"
            $sum = collect($items)->reduce(function ($sum, $item) use ($field) {
                return $sum + (isset($item['data'][$field]) ? (float) $item['data'][$field] : 0);
            }, 0);

            // Apply formatting based on field
            if ($field === 'sold') {
                $acc[$field] = $sum; // Keep as is or format as needed
            } else {
                $acc[$field] = '₹' . number_format($sum, 2); // Example: Format as currency with 2 decimal places
            }

            return $acc;
        }, []);
    }

    public function otaReport(Request $request)
    {
        $model = Booking::query()
            ->latest()
            ->filter(request('search'));

        $model->whereNot('source', null);


        if ($request->source && $request->source !== 'Select All') {
            $model->where('source', $request->source);
        }

        if ($request->payment_status && $request->payment_status !== 'Select All') {
            if ($request->payment_status == "Pending") {
                $model->where('balance', ">", 0);
            } else if ($request->payment_status == "Received") {
                $model->where('balance', 0);
            }
        }


        $model->WhereDate('check_in', '>=', $request->from);
        $model->whereDate('check_in', '<=', $request->to);


        return $model
            ->with([
                'bookedRooms:booking_id,id,room_no,room_type,booking_status',
                'customer:id,first_name,last_name,document,source_id',
            ])
            ->with([
                "bookedRooms" => function ($q) {
                    $q->withOut("booking");
                }
            ])
            ->where('company_id', $request->company_id)
            ->paginate($request->per_page ?? 20);
    }
}
