<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodPlan\ValidationRequest;
use App\Models\FoodPlan;

class FoodPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return FoodPlan::get();
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
    public function destroy(FoodPlan $foodPlan)
    {
        $foodPlan->delete();

        return response()->noContent();
    }
}
