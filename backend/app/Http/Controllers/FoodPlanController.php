<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodPlan\ValidationRequest;
use App\Models\BookedRoom;
use App\Models\Company;
use App\Models\FoodPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FoodPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return FoodPlan::where("is_for_hall", request("is_for_hall", FoodPlan::No))->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FoodPlan::paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        return FoodPlan::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FoodPlan  $foodPlan
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, FoodPlan $foodplan)
    {
        $foodplan->update($request->validated());

        return $foodplan;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FoodPlan  $foodPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FoodPlan::find($id)->delete();

        return response()->noContent();
    }

    public function getFoodPlanCount()
    {
        $found = DB::table('booked_rooms')
            ->where("company_id", request("company_id", 0))
            ->selectRaw('SUM(breakfast) as breakfast, SUM(lunch) as lunch, SUM(dinner) as dinner')
            ->first();

        return [
            "breakfast" => $found->breakfast ?? 0,
            "lunch" => $found->lunch ??  0,
            "dinner" => $found->dinner ??  0
        ];
    }

    public function getBookedFoodPlans()
    {
        return DB::table('booked_rooms')
            ->where("company_id", request("company_id", 0))
            ->get([
                "room_no",
                "room_type",
                "breakfast",
                "lunch",
                "dinner"
            ]);
    }


    public function FoodPlanPrint()
    {
        $payload = [
            "data" => $this->getBookedFoodPlans(),
            "company" => Company::find(request("company_id", 0)),
        ];

        return Pdf::loadView('food.list', $payload)
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function FoodPlanDownload()
    {
        $payload = [
            "data" => $this->getBookedFoodPlans(),
            "company" => Company::find(request("company_id", 0)),
        ];

        return Pdf::loadView('food.list', $payload)
            ->setPaper('a4', 'portrait')
            ->download();
    }
}
