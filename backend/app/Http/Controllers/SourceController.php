<?php

namespace App\Http\Controllers;

use App\Http\Requests\Source\StoreRequest;
use App\Http\Requests\Source\UpdateRequest;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{

    public function index(Request $request)
    {
        $request->company_id;
        $model = Source::query();
        $model->where('company_id', $request->company_id);

        if ($request->filled('search') && $request->search) {
            $model->where(function ($q) use ($request) {
                $q->orWhere('name', 'ILIKE', "%$request->search%");
                $q->orWhere('contact_name', 'ILIKE', "%$request->search%");
                $q->orWhere('mobile', 'ILIKE', "%$request->search%");
                $q->orWhere('gst', 'ILIKE', "%$request->search%");
                $q->orWhere('type', 'ILIKE', "%$request->search%");
            });
        }
        return $model->paginate(10 ?? $request->perPage);
        return response()->json(['sources' => $model->paginate(10 ?? $request->perPage), null, 'status' => true]);
    }

    public function getOnline(Request $request)
    {
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'online');
        $model->orderBy('name', 'asc');
        return $model->get();
    }
    public function getAgent(Request $request)
    {
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'agent');
        $model->orderBy('name', 'asc');
        return $model->get();
    }
    public function getCorporate(Request $request)
    {
        $model = Source::query();
        $model->where('company_id', $request->company_id);
        $model->where('type', 'corporate');
        $model->orderBy('name', 'asc');
        return $model->get();
    }

    public function store(StoreRequest $request)
    {
        $model = Source::query();
        $data  = $request->validated();

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
        $model->where('id', 'ILIKE', "%$key%");
        $model->where('company_id', $request->company_id);
        $model->orWhere('name', 'ILIKE', "%$key%");
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
