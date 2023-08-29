<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpensesCategories\StoreRequest;
use App\Http\Requests\ExpensesCategories\UpdateRequest;
use App\Models\ExpensesCategories;
use App\Http\Requests\StoreExpensesCategoriesRequest;
use App\Http\Requests\UpdateExpensesCategoriesRequest;
use Illuminate\Http\Request;

class ExpensesCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = ExpensesCategories::query();


        $model = $model->where('company_id', $request->company_id);

        //datatable Filters
        if ($request->filled('name')) {
            $model->where('name', 'like', "$request->TaxSlabs_no%");
        }

        //datatable sorty by
        if ($request->filled('sortBy')) {

            $sortDesc = $request->sortDesc === 'true' ? 'DESC' : 'ASC';
            if (strpos($request->sortBy, '.')) {
            } else {
                $model->orderBy($request->sortBy, $sortDesc);
            }
        } else {
            $model->orderBy('name', 'ASC');
        }

        return $model->paginate($request->per_page);
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
     * @param  \App\Http\Requests\StoreExpensesCategoriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data) {

                $verifyIsTaxSlabs = ExpensesCategories::where('company_id', $request->company_id)

                    ->where('name',  $request->name)

                    ->count();
                if ($verifyIsTaxSlabs == 0) {

                    $record = ExpensesCategories::create($data);

                    if ($record) {
                        return $this->response('Category details are successfully created', $record, true);
                    } else {
                        return $this->response('Category details not created', $record, false);
                    }
                } else {
                    return $this->response($request->start_price . '-' . $request->end_price  . ' : Category   is already exist. ', $data, false);
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
     * @param  \App\Models\ExpensesCategories  $expensesCategories
     * @return \Illuminate\Http\Response
     */
    public function show(ExpensesCategories $expensesCategories, $id)
    {
        return ExpensesCategories::where('id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpensesCategories  $expensesCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpensesCategories $expensesCategories, $id)
    {
        if (ExpensesCategories::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesCategoriesRequest  $request
     * @param  \App\Models\ExpensesCategories  $expensesCategories
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if ($data) {

                $isTaxSlabsExist = ExpensesCategories::where('company_id', $request->company_id)
                    ->where('name',  $request->name)
                    ->first();

                if ($isTaxSlabsExist) {
                    if ($isTaxSlabsExist->id != $id) {
                        return $this->response($request->start_price . '-' . $request->end_price . ' Category  Details are Can not save', null, false);
                    }
                }
                $status = ExpensesCategories::whereId($id)->update($data);
                if ($status) {
                    return $this->response('Category Details are updated succesfully', $status, true);
                } else {
                    return $this->response('Category Details are not Updated', $status, false);
                }
            } else {
                return $this->response('Error Occured', $data, false);
            }
        } catch (\Throwable $th) {
            return $this->response('Something wrong.', $th, false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpensesCategories  $expensesCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpensesCategories $expensesCategories, $id)
    {
        if (ExpensesCategories::find($id)->delete()) {

            return $this->response('Record    successfully deleted.', null, true);
        } else {
            return $this->response('Record   cannot delete.', null, false);
        }
    }
}
