 <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\PostingController;

    Route::resource('posting', PostingController::class);

    Route::get('posting/search/{key}', [PostingController::class, 'search']);


    Route::delete('posting_cancel/{posting}', [PostingController::class, 'cancel']);
    Route::get('getlast-posting-bill-number', [PostingController::class, 'getLastPostingBillNumber']);
    