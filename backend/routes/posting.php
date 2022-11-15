 <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\PostingController;

    Route::resource('posting', PostingController::class);

    Route::get('posting/search/{key}', [PostingController::class, 'search']);