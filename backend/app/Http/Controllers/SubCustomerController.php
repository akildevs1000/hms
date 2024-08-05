<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCustomer\StoreRequest;
use App\Http\Requests\SubCustomer\UpdateRequest;
use App\Models\SubCustomer;

class SubCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubCustomer::where("customer_id", request("customer_id"))->paginate(request("per_page", 15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return SubCustomer::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubCustomer  $subCustomer
     * @return \Illuminate\Http\Response
     */
    public function show(SubCustomer $subCustomer)
    {
        return $subCustomer->load("customer");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubCustomer  $subCustomer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        return SubCustomer::where("id", $id)->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubCustomer  $subCustomer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SubCustomer::where("id", $id)->delete();

        return response()->noContent();
    }
}
