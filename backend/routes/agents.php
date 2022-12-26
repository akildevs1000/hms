 <?php

    use App\Http\Controllers\AgentsController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\PostingController;

    Route::resource('agents', AgentsController::class);
    Route::get('city_ledger', [AgentsController::class, 'getCityLedger']);


    Route::get('get_agent_booking', [AgentsController::class, 'getAgentBookings']);
    Route::post('payment_by_agent', [AgentsController::class, 'paymentByAgent']);
    Route::get('get_agent_details', [AgentsController::class, 'getAgentDetails']);


    // Route::get('posting/search/{key}', [PostingController::class, 'search']);