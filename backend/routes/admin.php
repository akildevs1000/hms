<?php

use App\Http\Controllers\AssignModuleController;
use App\Http\Controllers\AssignPermissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceStatusController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TradeLicenseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// reset password
Route::post('/reset-password', [ResetPasswordController::class, 'sendCode']);
Route::post('/check-code', [ResetPasswordController::class, 'checkCode']);
Route::post('/new-password', [ResetPasswordController::class, 'newPassword']);

// Assign Permission
Route::post('assign-permission/delete/selected', [AssignPermissionController::class, 'dsr']);
Route::get('assign-permission/search/{key}', [AssignPermissionController::class, 'search']); // search records
Route::get('assign-permission/nars', [AssignPermissionController::class, 'notAssignedRoleIds']);
Route::resource('assign-permission', AssignPermissionController::class);

// User
Route::apiResource('users', UserController::class);
Route::get('users/search/{key}', [UserController::class, 'search']);
Route::post('users/delete/selected', [UserController::class, 'deleteSelected']);

//  Company
Route::get('company/list', [CompanyController::class, 'CompanyList']);

Route::apiResource('company', CompanyController::class)->except('update');
Route::post('company/{id}/update', [CompanyController::class, 'updateCompany']);
Route::post('company/{id}/update/contact', [CompanyController::class, 'updateContact']);
Route::post('company/{id}/update/user', [CompanyController::class, 'updateCompanyUser']);
Route::post('company/{id}/update/geographic', [CompanyController::class, 'updateCompanyGeographic']);
Route::post('company/validate', [CompanyController::class, 'validateCompany']);
Route::post('company/contact/validate', [CompanyController::class, 'validateContact']);
Route::post('company/user/validate', [CompanyController::class, 'validateCompanyUser']);
Route::post('company/update/user/validate', [CompanyController::class, 'validateCompanyUserUpdate']);
Route::get('company/search/{key}', [CompanyController::class, 'search']);
Route::get('company/{id}/branches', [CompanyController::class, 'branches']);
Route::get('company/{id}/devices', [CompanyController::class, 'devices']);

Route::post('company/{id}/trade-license', [TradeLicenseController::class, 'store']);

//  Permission
Route::apiResource('permission', PermissionController::class);
Route::get('user/{id}/permission', [PermissionController::class, 'permissions']);
Route::get('permission/search/{key}', [PermissionController::class, 'search']);
Route::post('permission/delete/selected', [PermissionController::class, 'deleteSelected']);

// Role
Route::apiResource('role', RoleController::class);
Route::get('user/{id}/role', [RoleController::class, 'roles']);
Route::get('role/search/{key}', [RoleController::class, 'search']);
Route::get('role/permissions/search/{key}', [RoleController::class, 'searchWithRelation']);
Route::get('role/{id}/permissions', [RoleController::class, 'getPermission']);
Route::post('role/{id}/permissions', [RoleController::class, 'assignPermission']);
Route::post('role/delete/selected', [RoleController::class, 'deleteSelected']);
// Branch
Route::apiResource('branch', BranchController::class)->except('update');
Route::post('branch/{id}/update', [BranchController::class, 'update']);
Route::post('branch/{id}/update/contact', [BranchController::class, 'updateContact']);
Route::post('branch/{id}/update/user', [BranchController::class, 'updateBranchUserUpdate']);
Route::post('branch/validate', [BranchController::class, 'validateBranch']);
Route::post('branch/contact/validate', [BranchController::class, 'validateContact']);
Route::post('branch/user/validate', [BranchController::class, 'validateBranchUser']);
Route::post('branch/update/user/validate', [BranchController::class, 'validateBranchUserUpdate']);
Route::get('branch/search/{key}', [BranchController::class, 'search']);

// Device
Route::apiResource('device', DeviceController::class);
Route::get('device/search/{key}', [DeviceController::class, 'search']);
Route::post('device/details', [DeviceController::class, 'getDeviceCompany']);
Route::get('device/getLastRecordsByCount/{company_id}/{count}', [DeviceController::class, 'getLastRecordsByCount']);
Route::post('device/delete/selected', [DeviceController::class, 'deleteSelected']);
Route::get('device_list', [DeviceController::class, 'getDeviceList']);

//  Device Status
Route::apiResource('device_status', DeviceStatusController::class);
Route::get('device_status/search/{key}', [DeviceStatusController::class, 'search']);
Route::post('device_status/delete/selected', [DeviceStatusController::class, 'deleteSelected']);

// Module
Route::apiResource('module', ModuleController::class);
Route::get('module/search/{key}', [ModuleController::class, 'search']);
Route::post('module/delete/selected', [ModuleController::class, 'deleteSelected']);

// Assign Permission
Route::post('assign-module/delete/selected', [AssignModuleController::class, 'dsr']);
Route::get('assign-module/search/{key}', [AssignModuleController::class, 'search']);
Route::get('assign-module/nacs', [AssignModuleController::class, 'notAssignedCompanyIds']);
Route::resource('assign-module', AssignModuleController::class);


//Testing Routes for Cron Jobs
Route::get('SyncCompanyIdsWithDevices', [AttendanceLogController::class, 'SyncCompanyIdsWithDevices']);

Route::get('SyncAttendance', [AttendanceController::class, 'SyncAttendance']);
Route::get('SyncAbsent', [AttendanceController::class, 'SyncAbsent']);
// Route::get('SyncAbsentForMultipleDays', [AttendanceController::class, 'SyncAbsentForMultipleDays']);