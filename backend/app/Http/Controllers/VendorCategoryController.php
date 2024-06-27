<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorCategory\ValidationRequest;
use App\Models\VendorCategory;

class VendorCategoryController extends Controller
{
    public function dropDown()
    {
        return VendorCategory::get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VendorCategory::paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        return VendorCategory::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorCategory  $vendorCategory
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, VendorCategory $vendorCategory)
    {
        $vendorCategory->update($request->validated());

        return $vendorCategory;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VendorCategory  $vendorCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorCategory $vendorCategory)
    {
        $vendorCategory->delete();

        return response()->noContent();
    }
}
