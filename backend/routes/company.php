<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignPermissionController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankInfoController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CountController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DocumentInfoController;
use App\Http\Controllers\DutyOrganizerController;
use App\Http\Controllers\EmiratesController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonalInfoController;
use App\Http\Controllers\policyController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\ReportNotificationController;
use App\Http\Controllers\Reports\AutoReportController;
use App\Http\Controllers\Reports\ManualReportController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SalaryTypeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScheduleEmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftTypeController;
use App\Http\Controllers\SubDepartmentController;
use App\Http\Controllers\TimeTableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// employee reporter
Route::post('/employee_to_reporter/{id}', [EmployeeController::class, 'employeeToReporter']);
Route::get('/employee_reporters', [EmployeeController::class, 'employeeReporters']);
Route::delete('/employee_remove_reporters/{id}', [EmployeeController::class, 'employeeRemoveReporter']);
Route::get('/reporter_by_employee/{id}', [EmployeeController::class, 'getReporterByEmployee']);
Route::get('/employee-count', [CountController::class, 'employeeDashboard']);

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

// User
Route::apiResource('users', UserController::class);
Route::get('users/search/{key}', [UserController::class, 'search']);
Route::post('users/delete/selected', [UserController::class, 'deleteSelected']);

// Department
Route::apiResource('departments', DepartmentController::class);
Route::get('departments/search/{key}', [DepartmentController::class, 'search']);
Route::post('departments/delete/selected', [DepartmentController::class, 'deleteSelected']);

// Sub Department
Route::apiResource('sub-departments', SubDepartmentController::class);
Route::post('sub-departments/delete/selected', [SubDepartmentController::class, 'deleteSelected']);
Route::get('sub-departments-by-department', [SubDepartmentController::class, 'sub_departments_by_department']);
Route::get('sub-departments-by-departments', [SubDepartmentController::class, 'sub_departments_by_departments']);

// Schedule
Route::apiResource('schedule', ScheduleController::class);
Route::get('schedule/search/{key}', [ScheduleController::class, 'search']);
Route::post('schedule/delete/selected', [ScheduleController::class, 'deleteSelected']);
Route::post('schedule_employee/delete/selected', [ScheduleEmployeeController::class, 'deleteSelected']);

// Designation
Route::apiResource('designation', DesignationController::class);
Route::get('designations-by-department', [DesignationController::class, 'designations_by_department']);
Route::get('designation/search/{key}', [DesignationController::class, 'search']);
Route::post('designation/delete/selected', [DesignationController::class, 'deleteSelected']);

// Role
Route::apiResource('role', RoleController::class);
Route::get('user/{id}/role', [RoleController::class, 'roles']);
Route::get('role/search/{key}', [RoleController::class, 'search']);
Route::get('role/permissions/search/{key}', [RoleController::class, 'searchWithRelation']);
Route::get('role/{id}/permissions', [RoleController::class, 'getPermission']);
Route::post('role/{id}/permissions', [RoleController::class, 'assignPermission']);
Route::post('role/delete/selected', [RoleController::class, 'deleteSelected']);

// AttendanceLogs
Route::apiResource('attendance_logs', AttendanceLogController::class);

Route::get('attendance_logs/{key}/daily', [AttendanceLogController::class, 'AttendanceLogsDaily']);
Route::get('attendance_logs/{key}/monthly', [AttendanceLogController::class, 'AttendanceLogsMonthly']);
Route::post('generate_manual_log', [AttendanceLogController::class, 'GenerateManualLog']);
Route::get('attendance_logs/search/{key}', [AttendanceLogController::class, 'search']);
Route::get('attendance_logs/{id}/search/{key}', [AttendanceLogController::class, 'AttendanceLogsSearch']);
Route::get('attendance_log_paginate/{page?}', [AttendanceLogController::class, 'AttendanceLogPaginate']);

Route::post('generate_logs', [AttendanceLogController::class, 'generate_logs']);
Route::get('logs', [AttendanceLogController::class, 'getAttendanceLogs']);

Route::get('attendance_single_list', [AttendanceLogController::class, 'singleView']);

// policy
Route::apiResource('policy', policyController::class);
Route::get('policy/search/{key}', [policyController::class, 'search']);
Route::post('policy/delete/selected', [policyController::class, 'deleteSelected']);

// announcement
Route::apiResource('announcement', AnnouncementController::class);
Route::get('announcement/search/{key}', [AnnouncementController::class, 'search']);
Route::post('announcement/delete/selected', [AnnouncementController::class, 'deleteSelected']);

Route::get('announcement/employee/{id}', [AnnouncementController::class, 'getAnnouncement']);

// activities
Route::apiResource('activity', ActivityController::class);

// -----------------------Company App-------------------------------

// Company Auth
Route::post('/CompanyLogin', [AuthController::class, 'CompanyLogin']);

Route::post('no-shift-employees/delete/selected', [DutyOrganizerController::class, 'deleteSelected']);
Route::get('no-shift-employees/search/{key}', [DutyOrganizerController::class, 'search']);

Route::apiResource('no-shift-employees', DutyOrganizerController::class);

//  Employee
Route::apiResource('employee', EmployeeController::class);

Route::get('employeesByDepartment', [EmployeeController::class, 'employeesByDepartment']);
Route::get('employeesBySubDepartment', [EmployeeController::class, 'employeesBySubDepartment']);
Route::get('employeesByDesignation/{key}', [EmployeeController::class, 'employeesByDesignation']);
Route::get('designationsByDepartment/{key}', [EmployeeController::class, 'designationsByDepartment']);
Route::get('scheduled_employees', [EmployeeController::class, 'scheduled_employees']);
Route::get('scheduled_employees_with_type', [EmployeeController::class, 'scheduled_employees_with_type']);
Route::get('attendance_employees', [EmployeeController::class, 'attendance_employees']);
Route::get('scheduled_employees/search/{key}', [EmployeeController::class, 'scheduled_employees_search']);

Route::get('not_scheduled_employees', [EmployeeController::class, 'not_scheduled_employees']);

Route::post('employee/validate', [EmployeeController::class, 'validateEmployee']);
Route::post('employee/contact/validate', [EmployeeController::class, 'validateContact']);
Route::post('employee/other/validate', [EmployeeController::class, 'validateOther']);
Route::post('employee/{id}/update', [EmployeeController::class, 'updateEmployee']);
Route::post('employee/{id}/update/contact', [EmployeeController::class, 'updateContact']);
Route::post('employee/{id}/update/other', [EmployeeController::class, 'updateOther']);
Route::get('employee/search/{key}', [EmployeeController::class, 'search']);
Route::post('employee/import', [EmployeeController::class, 'import']);
Route::resource('personalinfo', PersonalInfoController::class);
Route::resource('bankinfo', BankInfoController::class);
Route::resource('documentinfo', DocumentInfoController::class);
Route::resource('experience', ExperienceController::class);
Route::resource('qualification', QualificationController::class);
Route::resource('passport', PassportController::class);
Route::resource('visa', VisaController::class);
Route::resource('emirate', EmiratesController::class);

Route::post('employee/update/contact', [EmployeeController::class, 'employeeContactUpdate']);
Route::post('employee/update/setting', [EmployeeController::class, 'employeeUpdateSetting']);

// Salary Type
Route::apiResource('salary_type', SalaryTypeController::class);
Route::get('salary_type/search/{key}', [SalaryTypeController::class, 'search']);
Route::post('salary_type/delete/selected', [SalaryTypeController::class, 'deleteSelected']);

// Salary
Route::apiResource('salary', SalaryController::class);

// Deduction
Route::apiResource('deduction', DeductionController::class);

// Overtime
Route::apiResource('overtime', OvertimeController::class);

// Allowance
Route::apiResource('allowance', AllowanceController::class);

// Commission
Route::apiResource('commission', CommissionController::class);

Route::get('/count', CountController::class);

// dev started

Route::apiResource('shift', ShiftController::class);
Route::get('shift_by_type', [ShiftController::class, 'shift_by_type']);

Route::apiResource('time_table', TimeTableController::class);

Route::apiResource('schedule_employees', ScheduleEmployeeController::class);

Route::apiResource('shift_type', ShiftTypeController::class);

Route::get('custom_report', [ReportController::class, 'custom_report']);

Route::get('manual_report', [ManualReportController::class, 'custom_report']);
Route::post('manual_report', [ManualReportController::class, 'store']);
Route::get('auto_report', [AutoReportController::class, 'custom_report']);
Route::post('auto_report', [AutoReportController::class, 'store']);
Route::get('SyncDefaultAttendance', [AutoReportController::class, 'SyncDefaultAttendance']);

Route::get('no_report', [ReportController::class, 'no_report']);
Route::get('overnight_report', [ReportController::class, 'overnight_report']);
Route::get('odd_even_report', [ReportController::class, 'odd_even_report']);

Route::get('attendance_logs_details', [AttendanceLogController::class, 'AttendanceLogsDetails']);

Route::get('schedule_employees_logs', [ScheduleEmployeeController::class, 'logs']);
Route::get('employees_by_departments/{id}', [ScheduleEmployeeController::class, 'employees_by_departments']);

// -----------------------Employee App-------------------------------

//leave
Route::apiResource('leave', LeaveController::class);
Route::post('leave/delete/selected', [LeaveController::class, 'deleteSelected']);
Route::get('/leave-notification/{id}', [LeaveController::class, 'geLeaveNotification']);
Route::post('/leave-status', [LeaveController::class, 'status']);

Route::get('leave-notification/search/{key}/{id}', [LeaveController::class, 'searchNotification']); // search records
Route::get('leave/search/{key}', [LeaveController::class, 'search']); // search records


Route::post('report_notifications', function (Request $request) {
    return $request->all();
});


Route::apiResource('report_notification', ReportNotificationController::class);