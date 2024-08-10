<?php

namespace App\Http\Controllers;

use App\Models\BusinessSource;
use Illuminate\Http\Request;

class BusinessSourceController extends Controller
{
    public function dropDown()
    {
        return BusinessSource::whereCompanyId(request("company_id", 0))

            ->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BusinessSource::whereCompanyId(request("company_id", 0))

            ->where("name", "LIKE", "%" . request("search", null) . "%")
            
            ->paginate(request("per_page"));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|max:255",
            "slug" => "required",
            "company_id" => "required",
        ]);

        return BusinessSource::create($validated);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSource  $businessSource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessSource $businessSource)
    {
        $validated = $request->validate([
            "name" => "required|max:255",
            "slug" => "required",
            "company_id" => "required",
        ]);

        return $businessSource->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessSource  $businessSource
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessSource $businessSource)
    {
        $businessSource->delete();

        return response()->json();
    }
}
