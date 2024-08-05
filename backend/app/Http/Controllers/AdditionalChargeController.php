<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCharge;
use Illuminate\Http\Request;

class AdditionalChargeController extends Controller
{
    public function index()
    {
        $model = AdditionalCharge::query();

        $found = $model->where("company_id", request("company_id", 0))->first();

        if (!$found) {
            return [
                'early_check_in' => 0,
                'late_check_out' => 0,
                'extra_bed' => 0,
            ];
        }

        return $found;
    }
    public function store(Request $request)
    {
        $rules = [
            'early_check_in' => 'required|integer|min:0',
            'late_check_out' => 'required|integer|min:0',
            'extra_bed' => 'required|integer|min:0',
            'company_id' => 'required|integer|min:0',
        ];

        $messages = [
            'early_check_in.required' => 'The early check-in charge is required.',
            'early_check_in.integer' => 'The early check-in charge must be an integer.',
            'early_check_in.min' => 'The early check-in charge must be at least 0.',

            'late_check_out.required' => 'The late check-out charge is required.',
            'late_check_out.integer' => 'The late check-out charge must be an integer.',
            'late_check_out.min' => 'The late check-out charge must be at least 0.',

            'extra_bed.required' => 'The extra bed charge is required.',
            'extra_bed.integer' => 'The extra bed charge must be an integer.',
            'extra_bed.min' => 'The extra bed charge must be at least 0.',
        ];

        $request->validate($rules, $messages);


        $arr = [
            "early_check_in" => $request->early_check_in,
            "late_check_out" => $request->late_check_out,
            "extra_bed" => $request->extra_bed,
            "company_id" => $request->company_id,
        ];

        try {
            $model = AdditionalCharge::query();

            $found = $model->where("company_id", $request->company_id)->first();


            if ($found) {
                $model->update($arr);
            } else {
                $model->create($arr);
            }

            return response()->json($found, 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
