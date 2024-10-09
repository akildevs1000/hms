<?php

namespace App\Http\Controllers;

use App\Models\AuditHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuditHistoryController extends Controller
{
    public function getAuditReport(Request $request)
    {
        $date = $request->date ?? date("Y-m-d");
        $company_id = $request->company_id;
        // Fetch and group audit history by type
        $model = AuditHistory::whereCompanyId($company_id)
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy('type');

        return [
            "todayCheckIn" => $model["check_in"][0]["data"],
            "todayCheckInFileGenerateDateTime" => $model["check_in"][0]["dateTime"],
            "todayCheckInFilePath" =>  $this->getFilePath($company_id, $date, $model["check_in"][0]["file_name"]),

            "continueRooms" => $model["continue"][0]["data"],
            "continueRoomsFileGenerateDateTime" => $model["continue"][0]["dateTime"],
            "continueRoomsFilePath" =>  $this->getFilePath($company_id, $date, $model["continue"][0]["file_name"]),

            "todayCheckOut" => $model["check_out"][0]["data"],
            "todayCheckOutFileGenerateDateTime" => $model["check_out"][0]["dateTime"],
            "todayCheckOutFilePath" =>  $this->getFilePath($company_id, $date, $model["check_out"][0]["file_name"]),

            "todayPayments" => $model["payment"][0]["data"],
            "todayPaymentsFileGenerateDateTime" => $model["payment"][0]["dateTime"],
            "todayPaymentsFilePath" =>  $this->getFilePath($company_id, $date, $model["payment"][0]["file_name"]),

            "cityLedgerPaymentsAudit" => $model["cityLedger"][0]["data"],
            "cityLedgerPaymentsAuditFileGenerateDateTime" => $model["cityLedger"][0]["dateTime"],
            "cityLedgerPaymentsAuditFilePath" =>  $this->getFilePath($company_id, $date, $model["cityLedger"][0]["file_name"]),

            "cancelRooms" => $model["cancel"][0]["data"],
            "cancelRoomsFileGenerateDateTime" => $model["cancel"][0]["dateTime"],
            "cancelRoomsFilePath" =>  $this->getFilePath($company_id, $date, $model["cancel"][0]["file_name"]),


            "totExpense" => $model["expense"][0]["data"],
        ];
    }

    public function  getFilePath($company_id, $date, $file_name)
    {
        return storage_path("app/pdf/$date/$company_id/" . $file_name . ".pdf")  ?? null;
    }
}
