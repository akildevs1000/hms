<?php
namespace App\Http\Controllers;

use App\Http\Requests\Floor\ValidationRequest;
use App\Models\Floor;

class FloorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return Floor::where("company_id", request("company_id",0))->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Floor::where("company_id", request("company_id",0))->paginate(request("per_page", 50));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        return Floor::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Floor  $Floor
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Floor $Floor)
    {
        $Floor->update($request->validated());

        return $Floor;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Floor  $Floor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Floor::find($id)->delete();

        return response()->noContent();
    }
}
