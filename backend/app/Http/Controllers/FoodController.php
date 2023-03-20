<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = BookedRoom::query();
        $model->whereCompanyId($request->company_id);
        $model->without('booking');
        $model->whereHas('booking', function ($q) use ($request) {
            $q->where('booking_status', 2);
            $q->whereCompanyId($request->company_id);
        });

        $bookedRooms =   $model->get();
        $tem = [];

        foreach ($bookedRooms as $bookedRoom) {
            $breakfast = explode('|', $bookedRoom->meal)[0];
            $lunch = explode('|', $bookedRoom->meal)[1];
            $dinner = explode('|', $bookedRoom->meal)[2];
            if ($breakfast != '--- ') {
                $bookedRoom['breakfast'] = [
                    'title' => 'BreakFast',
                    'no_of_adult' => $bookedRoom->no_of_adult,
                    'no_of_child' => $bookedRoom->no_of_child,
                    'no_of_baby' => $bookedRoom->no_of_baby,
                ];
            }
            if ($lunch != ' --- ') {
                $bookedRoom['lunch'] = [
                    'title' => 'Lunch',
                    'no_of_adult' => $bookedRoom->no_of_adult,
                    'no_of_child' => $bookedRoom->no_of_child,
                    'no_of_baby' => $bookedRoom->no_of_baby,
                ];
            }

            if ($dinner != ' ---') {
                $bookedRoom['dinner'] = [
                    'title' => 'Dinner',
                    'no_of_adult' => $bookedRoom->no_of_adult,
                    'no_of_child' => $bookedRoom->no_of_child,
                    'no_of_baby' => $bookedRoom->no_of_baby,
                ];
            }

            $tem[] = $bookedRoom;
        }
        return $tem;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
