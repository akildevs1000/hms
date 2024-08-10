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

                'extra_hour' => 0,
                'cleaning' => 0,
                'electricity' => 0,
                'generator' => 0,
                'audio' => 0,
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


            'extra_hour' => 'required|integer|min:0',
            'cleaning' => 'required|integer|min:0',
            'electricity' => 'required|integer|min:0',
            'generator' => 'required|integer|min:0',
            'audio' => 'required|integer|min:0',

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


            'extra_hour.required' => 'The extra hour charge is required.',
            'extra_hour.integer' => 'The extra hour charge must be an integer.',
            'extra_hour.min' => 'The extra hour charge must be at least 0.',

            'cleaning.required' => 'The cleaning charge is required.',
            'cleaning.integer' => 'The cleaning charge must be an integer.',
            'cleaning.min' => 'The cleaning charge must be at least 0.',

            'electricity.required' => 'The electricity charge is required.',
            'electricity.integer' => 'The electricity charge must be an integer.',
            'electricity.min' => 'The electricity charge must be at least 0.',

            'generator.required' => 'The generator charge is required.',
            'generator.integer' => 'The generator charge must be an integer.',
            'generator.min' => 'The generator charge must be at least 0.',

            'audio.required' => 'The audio charge is required.',
            'audio.integer' => 'The audio charge must be an integer.',
            'audio.min' => 'The audio charge must be at least 0.',
        ];

        $request->validate($rules, $messages);


        $arr = [
            "early_check_in" => $request->early_check_in,
            "late_check_out" => $request->late_check_out,
            "extra_bed" => $request->extra_bed,

            "extra_hour" => $request->extra_hour,
            "cleaning" => $request->cleaning,
            "electricity" => $request->electricity,
            "generator" => $request->generator,
            "audio" => $request->audio,
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
