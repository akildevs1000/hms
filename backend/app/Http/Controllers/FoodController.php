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
        $model = $this->processCompany($request);
        return $this->processFoodOrderList($model->get());
    }

    public function processFoodOrderList($bookedRooms)
    {
        $tem = [];

        foreach ($bookedRooms as $bookedRoom) {

            $breakfast = explode('|', $bookedRoom->meal)[0];
            $lunch = explode('|', $bookedRoom->meal)[1];
            $dinner = explode('|', $bookedRoom->meal)[2];

            // if ($breakfast == '--- ' && $lunch == ' --- ' && $dinner == ' ---') {
            //     continue;
            // }

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
