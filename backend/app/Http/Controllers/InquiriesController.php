<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use App\Http\Requests\Inquiry\StoreRequest;
use App\Http\Requests\Inquiry\UpdateRequest;
use App\Models\Template;

class InquiriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Inquiry::query()->filter($request->search);
        $model->where('company_id', $request->company_id);
        return   $model->with("quotation")->paginate($request->per_page);;
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
                "rooms_type" => $inquiry->rooms_type,
                "email" => $inquiry->email
            ];

            if ($inquiry->email) {
                $this->sendMailIfRequired(Template::INQUERY_CREATE, $fields);
            }

            $this->sendWhatsappIfRequired(Template::INQUERY_CREATE, $fields);

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
