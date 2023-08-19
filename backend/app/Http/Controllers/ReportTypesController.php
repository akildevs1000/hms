<?php

namespace App\Http\Controllers;

use App\Models\ReportTypes;
use App\Http\Requests\StoreReportTypesRequest;
use App\Http\Requests\UpdateReportTypesRequest;

class ReportTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreReportTypesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReportTypesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReportTypes  $reportTypes
     * @return \Illuminate\Http\Response
     */
    public function show(ReportTypes $reportTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ReportTypes  $reportTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(ReportTypes $reportTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReportTypesRequest  $request
     * @param  \App\Models\ReportTypes  $reportTypes
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReportTypesRequest $request, ReportTypes $reportTypes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReportTypes  $reportTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReportTypes $reportTypes)
    {
        //
    }
}
