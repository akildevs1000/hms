<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;

class AgentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Agent::query();

        $model->where('company_id', $request->company_id);
        $model->where('type', '!=', 'Customer');
        $model->with('customer', 'booking:id,check_in,check_out,rooms');

        if ($request->filled('source') && $request->source != "" && $request->source != 'Select All') {
            $model->where('source', $request->source);
        }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->whereHas('booking', function ($q) use ($request) {
                $q->whereDate('check_in', '<=', $request->to);
                $q->WhereDate('check_out', '>=', $request->from);
            });
        }

        return  $model->paginate($request->per_page);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCityLedger(Request $request)
    {
        $model = Agent::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'Customer');
        $model->with('customer', 'booking:id,check_in,check_out,rooms');

        if ($request->filled('search') && $request->search != "") {

            $model->whereHas('customer', function ($q) use ($request) {
                $q->where('first_name', 'LIKE', "%$request->search%");
                $q->orWhere('last_name', 'LIKE', "%$request->search%");
            });

            if (is_numeric($request->search)) {
                $model->orWhereHas('booking', function ($q) use ($request) {
                    $q->where('id', $request->search);
                });
            }
        }



        return  $model->paginate($request->per_page ?? 20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($data)
    {
        $model = Agent::query();
        return  $model->create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}