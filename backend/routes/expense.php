<?php

use App\Http\Controllers\AdminExpenseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpensePaymentController;
use App\Http\Controllers\PaymentController;

Route::resource('expense', ExpenseController::class);
Route::get('expense/search/{key}', [ExpenseController::class, 'search']);
Route::get('management_expense', [ExpenseController::class, 'managementExpense']);

Route::get('account_count', [ExpenseController::class, 'counts']);

Route::resource('payments', PaymentController::class);
Route::get('expenses_documents/{key}', [ExpenseController::class, 'expensesDocuments']);
Route::post('expenses_document_delete/{key}', [ExpenseController::class, 'expensesDocumentsDelete']);
Route::get('expenses_statistics', [ExpenseController::class, 'getStaticstics']);

Route::resource('admin-expense', AdminExpenseController::class);
Route::get('payment-voucher/{id}', [AdminExpenseController::class, "voucher"]);

Route::resource('expense-payment', ExpensePaymentController::class);




Route::get('expense-count', [AdminExpenseController::class, "expenseCount"]);
Route::get('expense-last-number', [AdminExpenseController::class, "lastAdminExpenseNumber"]);

Route::get('get-last-three-records', [AdminExpenseController::class, "getLastThreeRecords"]);
