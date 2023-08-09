<?php

use App\Http\Controllers\AssignPermissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportNotificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// employee reporter
// reset password
Route::post('/reset-password', [ResetPasswordController::class, 'sendCode']);
Route::post('/check-code', [ResetPasswordController::class, 'checkCode']);
Route::post('/new-password', [ResetPasswordController::class, 'newPassword']);

// Assign Permission
Route::get('dropDownList', [PermissionController::class, 'dropDownList']);
Route::post('assign-permission/delete/selected', [AssignPermissionController::class, 'dsr']);
Route::get('assign-permission/search/{key}', [AssignPermissionController::class, 'search']); // search records
Route::get('assign-permission/nars', [AssignPermissionController::class, 'notAssignedRoleIds']);
Route::resource('assign-permission', AssignPermissionController::class);
Route::get('assign-permission/role-id/{key}', [AssignPermissionController::class, 'assignPermissionsByRoleid']);

// User
Route::apiResource('users', UserController::class);
Route::get('users/search/{key}', [UserController::class, 'search']);
Route::post('users/delete/selected', [UserController::class, 'deleteSelected']);

// Role
Route::apiResource('role', RoleController::class);
Route::get('user/{id}/role', [RoleController::class, 'roles']);
Route::get('role/search/{key}', [RoleController::class, 'search']);
Route::get('role/permissions/search/{key}', [RoleController::class, 'searchWithRelation']);
Route::get('role/{id}/permissions', [RoleController::class, 'getPermission']);
Route::post('role/{id}/permissions', [RoleController::class, 'assignPermission']);
Route::post('role/delete/selected', [RoleController::class, 'deleteSelected']);

// -----------------------Company App-------------------------------

// Company Auth
Route::post('/CompanyLogin', [AuthController::class, 'CompanyLogin']);

Route::post('report_notifications', function (Request $request) {
    return $request->all();
});

Route::apiResource('report_notification', ReportNotificationController::class);
