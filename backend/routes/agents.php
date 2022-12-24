 <?php

    use App\Http\Controllers\AgentsController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\PostingController;

    Route::resource('agents', AgentsController::class);

    // Route::get('posting/search/{key}', [PostingController::class, 'search']);