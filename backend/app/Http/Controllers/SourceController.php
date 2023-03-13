<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Requests\Source\StoreRequest;
use App\Http\Requests\Source\UpdateRequest;

class SourceController extends Controller
{


    public function index(Request  $request)
    {
        $request->company_id;
        $model = Source::query();
        $model->where('company_id', $request->company_id);

        if ($request->filled('search') && $request->search) {
            $model->where(function ($q) use ($request) {
                $q->orWhere('name', 'LIKE', "%$request->search%");
                $q->orWhere('contact_name', 'LIKE', "%$request->search%");
                $q->orWhere('mobile', 'LIKE', "%$request->search%");
                $q->orWhere('gst', 'LIKE', "%$request->search%");
                $q->orWhere('type', 'LIKE', "%$request->search%");
            });
        }
        return $model->paginate(10 ?? $request->perPage);
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
    public function getCorporate(Request  $request)
    {
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'corporate');
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

    public function update(UpdateRequest $request, Source $source)
    {
        try {
            $record = $source->update($request->validated());
            if ($record) {
                return $this->response('Source successfully updated.', 'null', true);
            } else {
                return $this->response('Source cannot update.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function search(Request $request, $key)
    {
        $model = Source::query();
        $model->where('id', 'LIKE', "%$key%");
        $model->where('company_id', $request->company_id);
        $model->orWhere('name', 'LIKE', "%$key%");
        return $model->paginate($request->per_page);
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
