<?php

namespace App\Http\Controllers;

use App\Models\WhatsappClient;
use Illuminate\Http\Request;

class WhatsappClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($company_id)
    {
        // WhatsappClient::truncate();
        
        $clients = WhatsappClient::where('company_id', $company_id)->first();
        return response()->json($clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $validated = $request->validate([
        //     'company_id' => 'required|exists:companies,id',
        //     'accounts' => 'required|array',
        // ]);

        // Check if a record exists for the given company_id
        $whatsappClient = WhatsappClient::where('company_id', $request->company_id)->first();

        if ($whatsappClient) {
            // Update existing record
            $whatsappClient->update(['accounts' => $request->accounts]);
        } else {
            // Create new record
            $whatsappClient = WhatsappClient::create(['company_id' => $request->company_id,'accounts' => $request->accounts]);
        }

        return response()->json($whatsappClient, 200);
    }
}
