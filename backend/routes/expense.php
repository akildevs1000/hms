 <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\ExpenseController;

    Route::resource('expense', ExpenseController::class);
    Route::get('expense/search/{key}', [ExpenseController::class, 'search']);

