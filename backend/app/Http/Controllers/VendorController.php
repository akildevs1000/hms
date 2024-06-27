<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vendor\ValidationRequest;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function dropDown()
    {
        return Vendor::get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Vendor::with("vendor_category")->paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        return Vendor::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Vendor $vendor)
    {
        $vendor->update($request->validated());

        return $vendor;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return response()->noContent();
    }
}
