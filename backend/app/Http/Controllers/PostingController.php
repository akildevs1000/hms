<?php

namespace App\Http\Controllers;

use App\Models\BookedRoom;
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
            ],
            'bookedRoom:id,room_no,room_type',
        ]);

        // filter base booking id (Reservation Number)
        $model->when($request->filled('booking_id'), function ($model) use ($request) {
            $model->whereHas('booking', function (Builder $query) use ($request) {
                $query->where('id', $request->booking_id);
            });
        });

        //filter base room id
        $model->when($request->filled('room_id'), function ($model) use ($request) {
            $model->whereHas('bookedRoom', function (Builder $query) use ($request) {
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
        $model->where('company_id', $request->company_id);

        // $fields = [
        //     'bill_no',
        // ];
        // $model = $this->process_search($model, $key, $fields);

        $model->with([
            'booking:id,customer_id,room_id' => [
                'customer:id,email,first_name,last_name',
            ],
            'bookedRoom:id,room_no,room_type',
        ]);

        $model->Where('bill_no', $key);
        $model->when($key && isset($key), function ($model) use ($key) {
            $model->orWhereHas('booking', function (Builder $query) use ($key) {
                $query->where('id', is_numeric($key) ? $key : 0)
                    ->orWhereHas('customer', function (Builder $query) use ($key) {
                        $query->where('first_name', 'Like', '%' . $key . '%')
                            ->orWhere('last_name', 'Like', '%' . $key . '%');
                    });
            });
        });

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
            $posting = Posting::create($data);

            $paymentsData = [
                'booking_id' => $posting->booking_id,
                'payment_mode' => 'cash',
                'description' => $posting->item,
                'amount' => $posting->amount_with_tax,
            ];
            $payment = new PaymentController();
            $payment->store($paymentsData);


            return $this->response('Posting Successfully submitted.', $posting, true);
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


        // $model = BookedRoom::where('company_id', $request->company_id)->find($id);
        // $model->customer = $model->title;
        // $model->makeHidden(['title', 'background']);
        // return $model;





        $model = Posting::query();
        $model->where('company_id', $request->company_id);

        $model->with([
            'bookedRoom',
            'booking:id,customer_id,room_id' => [
                'customer:id,email,first_name,last_name',
                'room:id,room_type_id,room_no',
            ]
        ]);

        return $model->whereBookedRoomId($id)->get();
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
