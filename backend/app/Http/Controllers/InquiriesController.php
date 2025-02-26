<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use App\Http\Requests\Inquiry\StoreRequest;
use App\Http\Requests\Inquiry\UpdateRequest;
use App\Models\Template;

class InquiriesController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = request()->input('from', null);
        $toDate = request()->input('to', null);
        $business_source_id = request()->input('business_source_id', 0);
        $source_id = request()->input('source_id', 0);
        $room_type_id = request()->input('room_type_id', 0);
        $quotation_ref = request()->input('quotation_ref', 0);

        $perPage = $request->per_page ?: 15; // Default to 15 if no per_page is provided

        // Query the Inquiry model
        $model = Inquiry::query();
        $model->where('company_id', $request->company_id);

        $model->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('created_at', [$fromDate . " 00:00:00", $toDate . " 23:59:59"]);
        });

        $model->when($business_source_id, function ($q) use ($business_source_id) {
            $q->where('business_source_id', $business_source_id);
        });

        $model->when($source_id, function ($q) use ($source_id) {
            $q->where('source_id', $source_id);
        });

        $model->when($room_type_id, function ($q) use ($room_type_id) {
            $q->where('room_type_id', $room_type_id);
        });

        $model->when($quotation_ref, function ($query) use ($quotation_ref) {
            $query->whereHas('quotation', function ($q) use ($quotation_ref) {
                $q->where('ref_no', env("WILD_CARD") ?? 'ILIKE', '%' . $quotation_ref . '%');
            });
        });

        // Load related models (optimizing query by eager loading)
        $model->with(['quotation', 'business_source', 'source', 'room_type']);

        // Apply sorting order
        $model->orderBy('id', 'desc');

        // Paginate the results
        $paginatedData = $model->paginate($perPage);

        // Return headers and paginated data
        return [
            'headers' => Inquiry::headers(),
            'paginatedData' => $paginatedData,
        ];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $inquiry = Inquiry::create($request->validated());

            $fields = [
                "title"     => $inquiry->title,
                "full_name" => $inquiry->full_name,
                "check_in"  => date('d-M-y', strtotime($inquiry->check_in)),
                "check_out" => date('d-M-y', strtotime($inquiry->check_out)),
                "rooms_type" => "------"
            ];

            if ($inquiry->email) {
                $fields["email"] = $inquiry->email;
                $this->sendMailIfRequired(Template::INQUERY_CREATE, $fields);
            }

            if ($inquiry->whatsapp) {
                $fields["whatsapp"] = $inquiry->whatsapp;
                $this->sendWhatsappIfRequired(Template::INQUERY_CREATE, $fields, $request->company_id);
            }


            return $this->response('Inquiry successfully added.', $inquiry->id, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function get_inquiry($id)
    {
        return Inquiry::where("contact_no", $id)->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            Inquiry::find($id)->update($request->validated());
            return $this->response('Inquiry successfully updated.', $id, true);
        } catch (\Throwable $th) {
            throw $th;
        }
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
