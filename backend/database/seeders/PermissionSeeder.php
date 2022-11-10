<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permission::truncate();


        // Permission::create(['module' => 'department', 'title' => 'department access', 'name' => 'department_access']);
        // Permission::create(['module' => 'department', 'title' => 'department view', 'name' => 'department_view']);
        // Permission::create(['module' => 'department', 'title' => 'department create', 'name' => 'department_create']);
        // Permission::create(['module' => 'department', 'title' => 'department edit', 'name' => 'department_edit']);
        // Permission::create(['module' => 'department', 'title' => 'department delete', 'name' => 'department_delete']);

        // Permission::create(['module' => 'sub_department', 'title' => 'sub department access', 'name' => 'sub_department_access']);
        // Permission::create(['module' => 'sub_department', 'title' => 'sub department view', 'name' => 'sub_department_view']);
        // Permission::create(['module' => 'sub_department', 'title' => 'sub department create', 'name' => 'sub_department_create']);
        // Permission::create(['module' => 'sub_department', 'title' => 'sub department edit', 'name' => 'sub_department_edit']);
        // Permission::create(['module' => 'sub_department', 'title' => 'sub department delete', 'name' => 'sub_department_delete']);

        // Permission::create(['module' => 'designation', 'title' => 'designation access', 'name' => 'designation_access']);
        // Permission::create(['module' => 'designation', 'title' => 'designation view', 'name' => 'designation_view']);
        // Permission::create(['module' => 'designation', 'title' => 'designation create', 'name' => 'designation_create']);
        // Permission::create(['module' => 'designation', 'title' => 'designation edit', 'name' => 'designation_edit']);
        // Permission::create(['module' => 'designation', 'title' => 'designation delete', 'name' => 'designation_delete']);

        // Permission::create(['module' => 'role', 'title' => 'role access', 'name' => 'role_access']);
        // Permission::create(['module' => 'role', 'title' => 'role view', 'name' => 'role_view']);
        // Permission::create(['module' => 'role', 'title' => 'role create', 'name' => 'role_create']);
        // Permission::create(['module' => 'role', 'title' => 'role edit', 'name' => 'role_edit']);
        // Permission::create(['module' => 'role', 'title' => 'role delete', 'name' => 'role_delete']);

        // Permission::create(['module' => 'assign_permission', 'title' => 'assign permission access', 'name' => 'assign_permission_access']);
        // Permission::create(['module' => 'assign_permission', 'title' => 'assign permission view', 'name' => 'assign_permission_view']);
        // Permission::create(['module' => 'assign_permission', 'title' => 'assign permission create', 'name' => 'assign_permission_create']);
        // Permission::create(['module' => 'assign_permission', 'title' => 'assign permission edit', 'name' => 'assign_permission_edit']);
        // Permission::create(['module' => 'assign_permission', 'title' => 'assign permission delete', 'name' => 'assign_permission_delete']);

        // Permission::create(['module' => 'employee', 'title' => 'employee access', 'name' => 'employee_access']);
        // Permission::create(['module' => 'employee', 'title' => 'employee view', 'name' => 'employee_view']);
        // Permission::create(['module' => 'employee', 'title' => 'employee create', 'name' => 'employee_create']);
        // Permission::create(['module' => 'employee', 'title' => 'employee edit', 'name' => 'employee_edit']);
        // Permission::create(['module' => 'employee', 'title' => 'employee delete', 'name' => 'employee_delete']);

        // Permission::create(['module' => 'announcement', 'title' => 'announcement access', 'name' => 'announcement_access']);
        // Permission::create(['module' => 'announcement', 'title' => 'announcement view', 'name' => 'announcement_view']);
        // Permission::create(['module' => 'announcement', 'title' => 'announcement create', 'name' => 'announcement_create']);
        // Permission::create(['module' => 'announcement', 'title' => 'announcement edit', 'name' => 'announcement_edit']);
        // Permission::create(['module' => 'announcement', 'title' => 'announcement delete', 'name' => 'announcement_delete']);

        // Permission::create(['module' => 'policy', 'title' => 'policy access', 'name' => 'policy_access']);
        // Permission::create(['module' => 'policy', 'title' => 'policy view', 'name' => 'policy_view']);
        // Permission::create(['module' => 'policy', 'title' => 'policy create', 'name' => 'policy_create']);
        // Permission::create(['module' => 'policy', 'title' => 'policy edit', 'name' => 'policy_edit']);
        // Permission::create(['module' => 'policy', 'title' => 'policy delete', 'name' => 'policy_delete']);

        //new
        Permission::create(['module' => 'attendance_report', 'title' => 'Attendance access', 'name' => 'attendance_report_access']);
        Permission::create(['module' => 'attendance_report', 'title' => 'Attendance view', 'name' => 'attendance_report_view']);
        Permission::create(['module' => 'attendance_report', 'title' => 'Attendance create', 'name' => 'attendance_report_create']);
        Permission::create(['module' => 'attendance_report', 'title' => 'Attendance edit', 'name' => 'attendance_report_edit']);
        Permission::create(['module' => 'attendance_report', 'title' => 'Attendance delete', 'name' => 'attendance_report_delete']);

        Permission::create(['module' => 'company_access', 'title' => 'Company access', 'name' => 'company_access']);
        Permission::create(['module' => 'company_access', 'title' => 'Company view', 'name' => 'company_view']);
        Permission::create(['module' => 'company_access', 'title' => 'Company create', 'name' => 'company_create']);
        Permission::create(['module' => 'company_access', 'title' => 'Company edit', 'name' => 'company_edit']);
        Permission::create(['module' => 'company_access', 'title' => 'Company delete', 'name' => 'company_delete']);

        Permission::create(['module' => 'device', 'title' => 'Device access', 'name' => 'device_access']);
        Permission::create(['module' => 'device', 'title' => 'Device view', 'name' => 'device_view']);
        Permission::create(['module' => 'device', 'title' => 'Device create', 'name' => 'device_create']);
        Permission::create(['module' => 'device', 'title' => 'Device edit', 'name' => 'device_edit']);
        Permission::create(['module' => 'device', 'title' => 'Device delete', 'name' => 'device_delete']);

        Permission::create(['module' => 'shift', 'title' => 'Shift access', 'name' => 'shift_access']);
        Permission::create(['module' => 'shift', 'title' => 'Shift view', 'name' => 'shift_view']);
        Permission::create(['module' => 'shift', 'title' => 'Shift create', 'name' => 'shift_create']);
        Permission::create(['module' => 'shift', 'title' => 'Shift edit', 'name' => 'shift_edit']);
        Permission::create(['module' => 'shift', 'title' => 'Shift delete', 'name' => 'shift_delete']);

        Permission::create(['module' => 'employee_schedule', 'title' => 'Employee_schedule access', 'name' => 'employee_schedule_access']);
        Permission::create(['module' => 'employee_schedule', 'title' => 'Employee_schedule view', 'name' => 'employee_schedule_view']);
        Permission::create(['module' => 'employee_schedule', 'title' => 'Employee_schedule create', 'name' => 'employee_schedule_create']);
        Permission::create(['module' => 'employee_schedule', 'title' => 'Employee_schedule edit', 'name' => 'employee_schedule_edit']);
        Permission::create(['module' => 'employee_schedule', 'title' => 'Employee_schedule delete', 'name' => 'employee_schedule_delete']);

        Permission::create(['module' => 'payroll', 'title' => 'Payroll access', 'name' => 'payroll_access']);
        Permission::create(['module' => 'payroll', 'title' => 'Payroll view', 'name' => 'payroll_view']);
        Permission::create(['module' => 'payroll', 'title' => 'Payroll create', 'name' => 'payroll_create']);
        Permission::create(['module' => 'payroll', 'title' => 'Payroll edit', 'name' => 'payroll_edit']);
        Permission::create(['module' => 'payroll', 'title' => 'Payroll delete', 'name' => 'payroll_delete']);

        Permission::create(['module' => 'setting', 'title' => 'Setting access', 'name' => 'setting_access']);
        Permission::create(['module' => 'setting', 'title' => 'Setting view', 'name' => 'setting_view']);
        Permission::create(['module' => 'setting', 'title' => 'Setting create', 'name' => 'setting_create']);
        Permission::create(['module' => 'setting', 'title' => 'Setting edit', 'name' => 'setting_edit']);
        Permission::create(['module' => 'setting', 'title' => 'Setting delete', 'name' => 'setting_delete']);

        Permission::create(['module' => 'notifications', 'title' => 'Notifications access', 'name' => 'notifications_access']);
        Permission::create(['module' => 'notifications', 'title' => 'Notifications view', 'name' => 'notifications_view']);
        Permission::create(['module' => 'notifications', 'title' => 'Notifications create', 'name' => 'notifications_create']);
        Permission::create(['module' => 'notifications', 'title' => 'Notifications edit', 'name' => 'notifications_edit']);
        Permission::create(['module' => 'notifications', 'title' => 'Notifications delete', 'name' => 'notifications_delete']);
    }
}