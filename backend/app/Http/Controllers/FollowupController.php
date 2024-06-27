<?php

namespace App\Http\Controllers;

use App\Http\Requests\Followup\ValidationRequest;
use App\Models\Followup;

class FollowupController extends Controller
{
    public function index()
    {
        return Followup::paginate(request("per_page", 50));
    }

    public function store(ValidationRequest $request)
    {
        $data = $request->validated();
        $data["date"] = date("d M Y H:i:s");
        return Followup::create($data);
    }
}
