<?php

namespace App\Http\Controllers;

use App\Models\AuditHistory;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AuditHistoryController extends Controller
{
    public function index(Request $request)
    {
        $company_id = $request->company_id;

        $startDate = Carbon::parse($request->from_date);
        $endDate = Carbon::parse($request->to_date);

        $dates = [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')];

        return AuditHistory::whereCompanyId($company_id)
            ->whereBetween('created_at', $dates)
            ->get();
        // ->groupBy('type')
        // ->map(fn($items) => $items->first());
    }

    public function getAuditReport(Request $request)
    {
        $companyId = $request->company_id ?? 0;
        $date = $request->date ?? date("Y-m-d");
        $cacheKey = "audit_report_{$companyId}_{$date}";

        // // Optional: Check if you want to delete the previous cache
        if ($request->has('delete_cache') && $request->delete_cache) {
            Cache::forget($cacheKey);
        }

        return Cache::rememberForever($cacheKey, function () use ($companyId, $date) {
            return AuditHistory::whereCompanyId($companyId)
                ->whereDate('created_at', $date)
                ->get()
                ->groupBy('type')
                ->map(fn($items) => $items->first());
        });
    }

    public function getAuditReportPrint(Request $request)
    {
        $filePath = $request->path;


        if (Storage::disk('local')->exists($filePath)) {
            // File exists, prepare the response
            return response()->file(storage_path('app/' . $filePath));
        } else {
            // File does not exist
            return response()->json(['message' => 'File not found'], 404);
        }

        $cacheKey = 'audit_report_' . md5($filePath); // Create a unique cache key based on the file path
        // Attempt to retrieve the cached response
        return Cache::rememberForever($cacheKey, function () use ($filePath) {
            // Check if the file exists
            if (Storage::disk('local')->exists($filePath)) {
                // File exists, prepare the response
                return response()->file(storage_path('app/' . $filePath));
            } else {
                // File does not exist
                return response()->json(['message' => 'File not found'], 404);
            }
        });
    }
}
