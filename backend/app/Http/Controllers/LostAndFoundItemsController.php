<?php

namespace App\Http\Controllers;

use App\Http\Requests\LostAndFoundItems\StoreRequest;
use App\Http\Requests\LostAndFoundItems\UpdateRequest;
use App\Models\Booking;
use App\Models\LostAndFoundItems;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LostAndFoundItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDefaultModelSettings($request, $id = '')
    {
        $model = LostAndFoundItems::query();

        $model->with(['booking.customer'])->where('company_id', $request->company_id);
        if ($id > 0) {
            $model->where('id', $id);
        }

        return $model;
    }
    public function index(Request $request)
    {
        $search = $request->search;

        $data = $this->getDefaultModelSettings($request);
        $data->where('booking_id', env("WILD_CARD") ?? 'ILIKE', "$search%");


        if (($search)) {
            $data->orWhere('item_name', env("WILD_CARD") ?? 'ILIKE', "$search%");
            $data->orWhere('status', env("WILD_CARD") ?? 'ILIKE', "$search%");
            $data->orWhereHas('booking.customer', function ($q) use ($search) {
                $q->where('first_name', env("WILD_CARD") ?? 'ILIKE', "$search%");
                $q->orWhere('last_name', env("WILD_CARD") ?? 'ILIKE', '%' . $search . '%');
            });
        }

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $data->WhereDate('missing_datetime', '>=', $request->from);
            $data->whereDate('missing_datetime', '<=', $request->to);
        }

        return $data->paginate($request->per_page ?? 100);
    }

    public function getStaticstics(Request $request)
    {


        $model = LostAndFoundItems::query();

        $model->with(['booking.customer'])->where('company_id', $request->company_id);
        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $model->WhereDate('missing_datetime', '>=', $request->from);
            $model->whereDate('missing_datetime', '<=', $request->to);
        }
        $model_copy = $model->clone();

        $missingTotal = $model_copy->count();
        $recovered = $model_copy->where('found_datetime', '!=', null)->count();
        $returned = $model_copy->where('returned_datetime', '!=', null)->count();

        return  ['missing' => $missingTotal, 'recovered' => $recovered, 'returned' => $returned];
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
     * @param  \App\Http\Requests\StoreLostAndFoundItemsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {

        try {
            $data = $request->validated();

            if ($data) {

                $record = LostAndFoundItems::create($data);

                //$verifyExistingData = LostAndFoundItems::where('booking_id', $data['booking_id'])->count();

                // if ($verifyExistingData == 0) {
                //     $record = LostAndFoundItems::create($data);
                // } else {
                //     return $this->response('Record already exist.', null, false);
                // }

                if ($record) {
                    return $this->response('Successfully created.', $record, true);
                } else {
                    return $this->response('Cannot create.', null, false);
                }
            } else {
                return $this->response('Data is not validated', $data, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LostAndFoundItems  $lostAndFoundItems
     * @return \Illuminate\Http\Response
     */
    public function show(LostAndFoundItems $lostAndFoundItems, $id)
    {

        $model = LostAndFoundItems::with(['booking.customer'])->where('id', $id)->first();

        return $model;
    }
    public function showRecord(Request $request, LostAndFoundItems $lostAndFoundItems, $id)
    {
        $lostItem = null;
        $booking = Booking::with(['customer'])->where('reservation_no', $id)->first();
        if ($request->missingItemId) {
            $lostItem = LostAndFoundItems::with(['booking'])->where('id', $request->missingItemId)->first();
        }

        return ["booking" => $booking, 'missingItem' => $lostItem];
    }

    public function searchBookingDetails(Request $request, $id)
    {

        $lostItem = null;
        $booking = Booking::with(['customer'])->where('reservation_no', $id)->where('company_id', $request->company_id)->first();
        if ($request->missingItemId) {
            $lostItem = LostAndFoundItems::with(['booking'])->where('id', $request->missingItemId)->first();
        }

        return ["booking" => $booking, 'missingItem' => $lostItem];
    }
    public function find($id)
    {
        return Booking::where('reservation_no', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LostAndFoundItems  $lostAndFoundItems
     * @return \Illuminate\Http\Response
     */
    public function edit(LostAndFoundItems $lostAndFoundItems)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLostAndFoundItemsRequest  $request
     * @param  \App\Models\LostAndFoundItems  $lostAndFoundItems
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, LostAndFoundItems $lostAndFoundItems, $id)
    {

        try {
            $record = null;
            if ($request['update_type'] == "missing") {
                $record = LostAndFoundItems::where('id', $id)->update([
                    'item_name' => $request['item_name'],
                    'missing_datetime' => $request['missing_datetime'],
                    'missing_remarks' => $request['missing_remarks'],
                    'missing_notes' => $request['missing_notes'],
                ]);
            } else if ($request['update_type'] == "found") {
                $record = LostAndFoundItems::where('id', $id)->update([
                    'found_datetime' => $request['found_datetime'],
                    'found_person_name' => $request['found_person_name'],
                    'found_location' => $request['found_location'],
                    'found_remarks' => $request['found_remarks'],
                    'found_notes' => $request['found_notes'],
                    'status' => 1,
                ]);
            } else if ($request['update_type'] == "return") {

                $record = LostAndFoundItems::where('id', $id)->update([
                    'returned_datetime' => $request['returned_datetime'],
                    'returned_person_name' => $request['returned_person_name'],
                    'returned_remarks' => $request['returned_remarks'],
                    'returned_notes' => $request['returned_notes'],
                    'approved_person_name' => $request['approved_person_name'],
                    'status' => 2,
                ]);
            }

            if ($record) {
                return $this->response('Successfully Updated.', $record, true);
            } else {
                return $this->response('No Update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LostAndFoundItems  $lostAndFoundItems
     * @return \Illuminate\Http\Response
     */
    public function destroy(LostAndFoundItems $lostAndFoundItems, $id)
    {
        if (LostAndFoundItems::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }

    public function uploadFoundImage(Request $request, $id)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = $request->booking_id . '_' . $id . '_found.' . $ext;
            $path = $file->storeAs('public/documents/customer/lost_and_found', $fileName);

            $record = LostAndFoundItems::where('id', $id)->update([
                'found_image' => $fileName,

            ]);

            return true;
        } else {
            return false;
        }
    }
    public function uploadReturnedImage(Request $request, $id)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $fileName = $request->booking_id . '_' . $id . '_returned.' . $ext;
            $path = $file->storeAs('public/documents/customer/lost_and_found', $fileName);

            $record = LostAndFoundItems::where('id', $id)->update([
                'recovered_image' => $fileName,

            ]);

            return true;
        } else {
            return false;
        }
    }
}
