<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Company;
use App\Models\BookedRoom;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $todayDate = date("Y-m-d");
        $model = BookedRoom::query();
        $model->whereCompanyId($request->company_id);
        $model->where(function ($query) use ($todayDate) {
            $query->whereDate('check_out', $todayDate)
                ->orWhereDate('check_in', $todayDate);
        });
        $model->without('booking');
        $model->with("foodplan");
        $model->whereIn('booking_status', [1, 2]);

        return $this->processFoodOrderList($model->get());
    }

    public function processFoodOrderList($bookedRooms)
    {
        $tem = [];

        foreach ($bookedRooms as $bookedRoom) {

            $breakfast =  $bookedRoom->foodplan->breakfast;
            $lunch =  $bookedRoom->foodplan->lunch;
            $dinner =  $bookedRoom->foodplan->dinner;
            $bookedRoom['breakfast'] = [
                'title' => 'BreakFast',
                'no_of_adult' => $bookedRoom->no_of_adult * $breakfast,
                'no_of_child' => $bookedRoom->no_of_child * $breakfast,
                'no_of_baby' => $bookedRoom->no_of_baby * $breakfast,
            ];
            $bookedRoom['lunch'] = [
                'title' => 'Lunch',
                'no_of_adult' => $bookedRoom->no_of_adult * $lunch,
                'no_of_child' => $bookedRoom->no_of_child * $lunch,
                'no_of_baby' => $bookedRoom->no_of_baby * $lunch,
            ];
            $bookedRoom['dinner'] = [
                'title' => 'Dinner',
                'no_of_adult' => $bookedRoom->no_of_adult * $dinner,
                'no_of_child' => $bookedRoom->no_of_child * $dinner,
                'no_of_baby' => $bookedRoom->no_of_baby * $dinner,
            ];

            $tem[] = $bookedRoom;
        }
        return $tem;
    }


    public function print(Request $request)
    {
        $model = $this->processCompany($request);
        $item = $this->processFoodOrderList($model->get());
        return Pdf::loadView('food.list', ['data' => $item, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function download(Request $request)
    {
        $model = $this->processCompany($request);
        $item = $this->processFoodOrderList($model->get());
        return Pdf::loadView('food.list', ['data' => $item, 'company' => Company::find($request->company_id)])
            ->setPaper('a4', 'portrait')
            ->download();
    }


    public function processCompany($request)
    {
        $model = BookedRoom::query();
        $model->whereCompanyId($request->company_id);
        $model->where(function ($q) {
            $q->where('tot_adult_food', '>', 0);
            $q->orWhere('tot_child_food', '>', 0);
        });
        $model->without('booking');
        $model->whereHas('booking', function ($q) use ($request) {
            $q->where('booking_status', 2);
            $q->whereCompanyId($request->company_id);
        });

        return $model;
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
