<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vendor\ValidationRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function vendorSearch(Request $request)
    {
        $search = $request->search;
        $company_id = $request->company_id;

        $query = Vendor::query();
        $query->where("company_id",$company_id);
        $query->when($search ?? false, fn($query, $search) =>
        $query->where(
            fn($query) => $query
                ->orWhere('company_name', $search)
                ->orWhere('first_name', $search)
                ->orWhere('last_name', $search)
                ->orWhere('mobile', $search)
        ));

        return $query->with("vendor_category")->first();
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
