<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Requests\Source\StoreRequest;

class SourceController extends Controller
{


    public function index(Request  $request)
    {
        $request->company_id;
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        // return    $model->count();
        return response()->json(['sources' => $model->paginate(10 ?? $request->perPage), null, 'status' => true]);
    }

    public function getOnline(Request  $request)
    {
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'online');
        return $model->get();
    }
    public function getAgent(Request  $request)
    {
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'agent');
        return $model->get();
    }

    public function store(StoreRequest $request)
    {
        $model = Source::query();
        $data = $request->validated();

        try {
            $record = $model->create($data);

            if ($record) {
                return $this->response('Source successfully added.', null, true);
            } else {
                return $this->response('Source cannot add.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy($id)
    {
        $record = Source::find($id);
        if ($record->delete()) {
            return $this->response('Source successfully deleted.', null, true);
        } else {
            return $this->response('Source successfully deleted.', null, true);
        }
    }
}