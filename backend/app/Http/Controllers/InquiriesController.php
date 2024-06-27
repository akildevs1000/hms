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
        return   $model->paginate($request->per_page);;
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
    public function store(StoreRequest $request)
    {
        try {
            $inquiry = Inquiry::whereContactNo($request->contact_no)->first();
            $id = "";

            if ($inquiry) {
                $id = $inquiry->id;
                $inquiry->update($request->validated());
            } else {
                $inquiry = Inquiry::create($request->validated());
                $id = $inquiry->id;
            }

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

            return $this->response('Inquiry successfully added.', $id, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
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
