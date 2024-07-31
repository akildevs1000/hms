<?php

namespace App\Http\Controllers;

use App\Models\BookingSourceType;
use Illuminate\Http\Request;

class BookingSourceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BookingSourceType::paginate(request("per_page", 15));
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
            "description" => "nullable|max:255",
        ]);
        return BookingSourceType::create($validated);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookingSourceType  $bookingSourceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookingSourceType $bookingSourceType)
    {
        $validated = $request->validate([
            "name" => "required|max:255",
            "description" => "nullable|max:255",
        ]);
        
        $bookingSourceType->update($validated);

        return $bookingSourceType;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookingSourceType  $bookingSourceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookingSourceType $bookingSourceType)
    {
        $bookingSourceType->delete();

        return response()->noContent();
    }
}
