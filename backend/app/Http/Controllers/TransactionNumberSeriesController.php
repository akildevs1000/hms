<?php
namespace App\Http\Controllers;

use App\Models\TransactionNumberSeries;
use Exception;
use Illuminate\Http\Request;

class TransactionNumberSeriesController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
            ]);

            // Create or update the mail settings
            $data = TransactionNumberSeries::updateOrCreate(
                ['company_id' => $validated['company_id']],
                ["company_id" => $request->company_id, "json" => $request->json]
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaction number series saved successfully.',
                'data'    => $data,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save transaction number series.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
            ]);

            $model = TransactionNumberSeries::where('company_id', $request->company_id)->first();

            if (! $model) {
                return response()->json(["json" => []]);
            }

            return response()->json($model);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching SMTP config.', 'error' => $e->getMessage()], 500);
        }
    }
}
