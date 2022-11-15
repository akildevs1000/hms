<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Posting;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PostingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Posting::query();
        $model->where('company_id', $request->company_id);

        $model->with([
            'booking:id,customer_id,room_id' => [
                'customer:id,email,first_name,last_name',
                'room:id,room_type_id,room_no',
            ]
        ]);

        // filter base booking id
        $model->when($request->filled('booking_id'), function ($model) use ($request) {
            $model->whereHas('booking', function (Builder $query) use ($request) {
                $query->where('id', $request->booking_id);
            });
        });

        //filter base room id
        $model->when($request->filled('room_id'), function ($model) use ($request) {
            $model->whereHas('booking', function (Builder $query) use ($request) {
                $query->where('room_id', $request->room_id);
            });
        });

        //filter base customer id
        // $model->when($request->filled('customer_id'), function ($model) use ($request) {
        //     $model->whereHas('booking', function (Builder $query) use ($request) {
        //         $query->where('customer_id', $request->customer_id);
        //     });
        // });

        return $model->paginate($request->per_page ?? 50);
    }

    public function search(Request $request, $key)
    {
        $model = Posting::query();

        $fields = [
            'bill_no',
        ];

        $model = $this->process_search($model, $key, $fields);
        $model->where('company_id', $request->company_id);
        $model->with([
            'booking:id,customer_id,room_id' => [
                'customer:id,email,first_name,last_name',
                'room:id,room_type_id,room_no',
            ]
        ]);

        return $model->paginate($request->per_page ?? 50);
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
        try {
            $data = $request->all();
            $data['posting_date'] = now();
            $data = Posting::create($data);
            return $this->response('Posting Successfully submitted.', $data, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = Posting::query();
        $model->where('company_id', $request->company_id);

        $model->with([
            'booking:id,customer_id,room_id' => [
                'customer:id,email,first_name,last_name',
                'room:id,room_type_id,room_no',
            ]
        ]);

        return $model->whereBookingId($id)->get();
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