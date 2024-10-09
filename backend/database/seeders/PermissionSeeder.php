<?php

namespace Database\Seeders;

use App\Models\AssignPermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=PermissionSeeder
     *
     * @return void
     */
    public function run()
    {
        Permission::truncate();
        AssignPermission::truncate();

        Permission::create(['module' => 'home', 'title' => 'Access', 'name' => 'home_access']);
        Permission::create(['module' => 'home', 'title' => 'View', 'name' => 'home_view']);
        Permission::create(['module' => 'home', 'title' => 'Create', 'name' => 'home_create']);
        Permission::create(['module' => 'home', 'title' => 'Edit', 'name' => 'home_edit']);
        Permission::create(['module' => 'home', 'title' => 'Delete', 'name' => 'home_delete']);

        Permission::create(['module' => 'calendar', 'title' => 'Access', 'name' => 'calendar_access']);
        Permission::create(['module' => 'calendar', 'title' => 'View', 'name' => 'calendar_view']);
        Permission::create(['module' => 'calendar', 'title' => 'Create', 'name' => 'calendar_create']);
        Permission::create(['module' => 'calendar', 'title' => 'Edit', 'name' => 'calendar_edit']);
        Permission::create(['module' => 'calendar', 'title' => 'Delete', 'name' => 'calendar_delete']);

        Permission::create(['module' => 'history', 'title' => 'Access', 'name' => 'history_access']);
        Permission::create(['module' => 'history', 'title' => 'View', 'name' => 'history_view']);
        Permission::create(['module' => 'history', 'title' => 'Create', 'name' => 'history_create']);
        Permission::create(['module' => 'history', 'title' => 'Edit', 'name' => 'history_edit']);
        Permission::create(['module' => 'history', 'title' => 'Delete', 'name' => 'history_delete']);

        Permission::create(['module' => 'night_audit', 'title' => 'Access', 'name' => 'night_audit_access']);
        Permission::create(['module' => 'night_audit', 'title' => 'View', 'name' => 'night_audit_view']);
        Permission::create(['module' => 'night_audit', 'title' => 'Create', 'name' => 'night_audit_create']);
        Permission::create(['module' => 'night_audit', 'title' => 'Edit', 'name' => 'night_audit_edit']);
        Permission::create(['module' => 'night_audit', 'title' => 'Delete', 'name' => 'night_audit_delete']);

        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Access', 'name' => 'lost_and_found_access']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'View', 'name' => 'lost_and_found_view']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Create', 'name' => 'lost_and_found_create']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Edit', 'name' => 'lost_and_found_edit']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Delete', 'name' => 'lost_and_found_delete']);

        Permission::create(['module' => 'customers', 'title' => 'Access', 'name' => 'customers_access']);
        Permission::create(['module' => 'customers', 'title' => 'View', 'name' => 'customers_view']);
        Permission::create(['module' => 'customers', 'title' => 'Create', 'name' => 'customers_create']);
        Permission::create(['module' => 'customers', 'title' => 'Edit', 'name' => 'customers_edit']);
        Permission::create(['module' => 'customers', 'title' => 'Delete', 'name' => 'customers_delete']);

        Permission::create(['module' => 'inquiry', 'title' => 'Access', 'name' => 'inquiry_access']);
        Permission::create(['module' => 'inquiry', 'title' => 'View', 'name' => 'inquiry_view']);
        Permission::create(['module' => 'inquiry', 'title' => 'Create', 'name' => 'inquiry_create']);
        Permission::create(['module' => 'inquiry', 'title' => 'Edit', 'name' => 'inquiry_edit']);
        Permission::create(['module' => 'inquiry', 'title' => 'Delete', 'name' => 'inquiry_delete']);

        Permission::create(['module' => 'quotation', 'title' => 'Access', 'name' => 'quotation_access']);
        Permission::create(['module' => 'quotation', 'title' => 'View', 'name' => 'quotation_view']);
        Permission::create(['module' => 'quotation', 'title' => 'Create', 'name' => 'quotation_create']);
        Permission::create(['module' => 'quotation', 'title' => 'Edit', 'name' => 'quotation_edit']);
        Permission::create(['module' => 'quotation', 'title' => 'Delete', 'name' => 'quotation_delete']);

        Permission::create(['module' => 'invoices', 'title' => 'Access', 'name' => 'invoices_access']);
        Permission::create(['module' => 'invoices', 'title' => 'View', 'name' => 'invoices_view']);
        Permission::create(['module' => 'invoices', 'title' => 'Create', 'name' => 'invoices_create']);
        Permission::create(['module' => 'invoices', 'title' => 'Edit', 'name' => 'invoices_edit']);
        Permission::create(['module' => 'invoices', 'title' => 'Delete', 'name' => 'invoices_delete']);

        Permission::create(['module' => 'analytics', 'title' => 'Access', 'name' => 'analytics_access']);
        Permission::create(['module' => 'analytics', 'title' => 'View', 'name' => 'analytics_view']);
        Permission::create(['module' => 'analytics', 'title' => 'Create', 'name' => 'analytics_create']);
        Permission::create(['module' => 'analytics', 'title' => 'Edit', 'name' => 'analytics_edit']);
        Permission::create(['module' => 'analytics', 'title' => 'Delete', 'name' => 'analytics_delete']);

        Permission::create(['module' => 'city_ledger', 'title' => 'Access', 'name' => 'city_ledger_access']);
        Permission::create(['module' => 'city_ledger', 'title' => 'View', 'name' => 'city_ledger_view']);
        Permission::create(['module' => 'city_ledger', 'title' => 'Create', 'name' => 'city_ledger_create']);
        Permission::create(['module' => 'city_ledger', 'title' => 'Edit', 'name' => 'city_ledger_edit']);
        Permission::create(['module' => 'city_ledger', 'title' => 'Delete', 'name' => 'city_ledger_delete']);

        Permission::create(['module' => 'posting', 'title' => 'Access', 'name' => 'posting_access']);
        Permission::create(['module' => 'posting', 'title' => 'View', 'name' => 'posting_view']);
        Permission::create(['module' => 'posting', 'title' => 'Create', 'name' => 'posting_create']);
        Permission::create(['module' => 'posting', 'title' => 'Edit', 'name' => 'posting_edit']);
        Permission::create(['module' => 'posting', 'title' => 'Delete', 'name' => 'posting_delete']);

        Permission::create(['module' => 'expense', 'title' => 'Access', 'name' => 'expense_access']);
        Permission::create(['module' => 'expense', 'title' => 'View', 'name' => 'expense_view']);
        Permission::create(['module' => 'expense', 'title' => 'Create', 'name' => 'expense_create']);
        Permission::create(['module' => 'expense', 'title' => 'Edit', 'name' => 'expense_edit']);
        Permission::create(['module' => 'expense', 'title' => 'Delete', 'name' => 'expense_delete']);

        Permission::create(['module' => 'vendors', 'title' => 'Access', 'name' => 'vendors_access']);
        Permission::create(['module' => 'vendors', 'title' => 'View', 'name' => 'vendors_view']);
        Permission::create(['module' => 'vendors', 'title' => 'Create', 'name' => 'vendors_create']);
        Permission::create(['module' => 'vendors', 'title' => 'Edit', 'name' => 'vendors_edit']);
        Permission::create(['module' => 'vendors', 'title' => 'Delete', 'name' => 'vendors_delete']);

        Permission::create(['module' => 'gst_bills', 'title' => 'Access', 'name' => 'gst_access']);
        Permission::create(['module' => 'gst_bills', 'title' => 'View', 'name' => 'gst_view']);
        Permission::create(['module' => 'gst_bills', 'title' => 'Create', 'name' => 'gst_create']);
        Permission::create(['module' => 'gst_bills', 'title' => 'Edit', 'name' => 'gst_edit']);
        Permission::create(['module' => 'gst_bills', 'title' => 'Delete', 'name' => 'gst_delete']);

        Permission::create(['module' => 'income', 'title' => 'Access', 'name' => 'income_access']);
        Permission::create(['module' => 'income', 'title' => 'View', 'name' => 'income_view']);
        Permission::create(['module' => 'income', 'title' => 'Create', 'name' => 'income_create']);
        Permission::create(['module' => 'income', 'title' => 'Edit', 'name' => 'income_edit']);
        Permission::create(['module' => 'income', 'title' => 'Delete', 'name' => 'income_delete']);

        Permission::create(['module' => 'management_payments', 'title' => 'Access', 'name' => 'management_payments_access']);
        Permission::create(['module' => 'management_payments', 'title' => 'View', 'name' => 'management_payments_view']);
        Permission::create(['module' => 'management_payments', 'title' => 'Create', 'name' => 'management_payments_create']);
        Permission::create(['module' => 'management_payments', 'title' => 'Edit', 'name' => 'management_payments_edit']);
        Permission::create(['module' => 'management_payments', 'title' => 'Delete', 'name' => 'management_payments_delete']);

        Permission::create(['module' => 'management_summary', 'title' => 'Access', 'name' => 'management_summary_access']);
        Permission::create(['module' => 'management_summary', 'title' => 'View', 'name' => 'management_summary_view']);
        Permission::create(['module' => 'management_summary', 'title' => 'Create', 'name' => 'management_summary_create']);
        Permission::create(['module' => 'management_summary', 'title' => 'Edit', 'name' => 'management_summary_edit']);
        Permission::create(['module' => 'management_summary', 'title' => 'Delete', 'name' => 'management_summary_delete']);

        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Access', 'name' => 'management_soldout_access']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut View', 'name' => 'management_soldout_view']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Create', 'name' => 'management_soldout_create']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Edit', 'name' => 'management_soldout_edit']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Delete', 'name' => 'management_soldout_delete']);

        Permission::create(['module' => 'management_revenue_report', 'title' => 'Access', 'name' => 'management_revenue_report_access']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'View', 'name' => 'management_revenue_report_view']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Create', 'name' => 'management_revenue_report_create']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Edit', 'name' => 'management_revenue_report_edit']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Delete', 'name' => 'management_revenue_report_delete']);

        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Access', 'name' => 'management_top_10_customers_access']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'View', 'name' => 'management_top_10_customers_view']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Create', 'name' => 'management_top_10_customers_create']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Edit', 'name' => 'management_top_10_customers_edit']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Delete', 'name' => 'management_top_10_customers_delete']);

        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Access', 'name' => 'management_custom_soldout_access']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'View', 'name' => 'management_custom_soldout_view']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Create', 'name' => 'management_custom_soldout_create']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Edit', 'name' => 'management_custom_soldout_edit']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Delete', 'name' => 'management_custom_soldout_delete']);

        Permission::create(['module' => 'tax', 'title' => 'Access', 'name' => 'tax_access']);
        Permission::create(['module' => 'tax', 'title' => 'View', 'name' => 'tax_view']);
        Permission::create(['module' => 'tax', 'title' => 'Create', 'name' => 'tax_create']);
        Permission::create(['module' => 'tax', 'title' => 'Edit', 'name' => 'tax_edit']);
        Permission::create(['module' => 'tax', 'title' => 'Delete', 'name' => 'tax_delete']);

        Permission::create(['module' => 'company', 'title' => 'Access', 'name' => 'company_access']);
        Permission::create(['module' => 'company', 'title' => 'View', 'name' => 'company_view']);
        Permission::create(['module' => 'company', 'title' => 'Create', 'name' => 'company_create']);
        Permission::create(['module' => 'company', 'title' => 'Edit', 'name' => 'company_edit']);
        Permission::create(['module' => 'company', 'title' => 'Delete', 'name' => 'company_delete']);

        Permission::create(['module' => 'automation', 'title' => 'Access', 'name' => 'automation_access']);
        Permission::create(['module' => 'automation', 'title' => 'View', 'name' => 'automation_view']);
        Permission::create(['module' => 'automation', 'title' => 'Create', 'name' => 'automation_create']);
        Permission::create(['module' => 'automation', 'title' => 'Edit', 'name' => 'automation_edit']);
        Permission::create(['module' => 'automation', 'title' => 'Delete', 'name' => 'automation_delete']);

        Permission::create(['module' => 'rooms', 'title' => 'Access', 'name' => 'rooms_access']);
        Permission::create(['module' => 'rooms', 'title' => 'View', 'name' => 'rooms_view']);
        Permission::create(['module' => 'rooms', 'title' => 'Create', 'name' => 'rooms_create']);
        Permission::create(['module' => 'rooms', 'title' => 'Edit', 'name' => 'rooms_edit']);
        Permission::create(['module' => 'rooms', 'title' => 'Delete', 'name' => 'rooms_delete']);

        Permission::create(['module' => 'hall', 'title' => 'Access', 'name' => 'hall_access']);
        Permission::create(['module' => 'hall', 'title' => 'View', 'name' => 'hall_view']);
        Permission::create(['module' => 'hall', 'title' => 'Create', 'name' => 'hall_create']);
        Permission::create(['module' => 'hall', 'title' => 'Edit', 'name' => 'hall_edit']);
        Permission::create(['module' => 'hall', 'title' => 'Delete', 'name' => 'hall_delete']);

        Permission::create(['module' => 'rooms_category', 'title' => 'Access', 'name' => 'rooms_category_access']);
        Permission::create(['module' => 'rooms_category', 'title' => 'View', 'name' => 'rooms_category_view']);
        Permission::create(['module' => 'rooms_category', 'title' => 'Create', 'name' => 'rooms_category_create']);
        Permission::create(['module' => 'rooms_category', 'title' => 'Edit', 'name' => 'rooms_category_edit']);
        Permission::create(['module' => 'rooms_category', 'title' => 'Delete', 'name' => 'rooms_category_delete']);

        Permission::create(['module' => 'price_setup', 'title' => 'Access', 'name' => 'price_setup_access']);
        Permission::create(['module' => 'price_setup', 'title' => 'View', 'name' => 'price_setup_view']);
        Permission::create(['module' => 'price_setup', 'title' => 'Create', 'name' => 'price_setup_create']);
        Permission::create(['module' => 'price_setup', 'title' => 'Edit', 'name' => 'price_setup_edit']);
        Permission::create(['module' => 'price_setup', 'title' => 'Delete', 'name' => 'price_setup_delete']);

        Permission::create(['module' => 'setup', 'title' => 'Access', 'name' => 'setup_access']);
        Permission::create(['module' => 'setup', 'title' => 'View', 'name' => 'setup_view']);
        Permission::create(['module' => 'setup', 'title' => 'Create', 'name' => 'setup_create']);
        Permission::create(['module' => 'setup', 'title' => 'Edit', 'name' => 'setup_edit']);
        Permission::create(['module' => 'setup', 'title' => 'Delete', 'name' => 'setup_delete']);

        Permission::create(['module' => 'employee', 'title' => 'Access', 'name' => 'employee_access']);
        Permission::create(['module' => 'employee', 'title' => 'View', 'name' => 'employee_view']);
        Permission::create(['module' => 'employee', 'title' => 'Create', 'name' => 'employee_create']);
        Permission::create(['module' => 'employee', 'title' => 'Edit', 'name' => 'employee_edit']);
        Permission::create(['module' => 'employee', 'title' => 'Delete', 'name' => 'employee_delete']);

        Permission::create(['module' => 'device', 'title' => 'Access', 'name' => 'device_access']);
        Permission::create(['module' => 'device', 'title' => 'View', 'name' => 'device_view']);
        Permission::create(['module' => 'device', 'title' => 'Create', 'name' => 'device_create']);
        Permission::create(['module' => 'device', 'title' => 'Edit', 'name' => 'device_edit']);
        Permission::create(['module' => 'device', 'title' => 'Delete', 'name' => 'device_delete']);

        Permission::create(['module' => 'role', 'title' => 'Access', 'name' => 'role_access']);
        Permission::create(['module' => 'role', 'title' => 'View', 'name' => 'role_view']);
        Permission::create(['module' => 'role', 'title' => 'Create', 'name' => 'role_create']);
        Permission::create(['module' => 'role', 'title' => 'Edit', 'name' => 'role_edit']);
        Permission::create(['module' => 'role', 'title' => 'Delete', 'name' => 'role_delete']);


        // Permission::create(['module' => 'setting_access', 'title' => 'Settings Access', 'name' => 'setting_access']);
        // Permission::create(['module' => 'history_menu', 'title' => 'History Menu Access', 'name' => 'history_menu_access']);
        // Permission::create(['module' => 'guest_menu', 'title' => 'Guest menu Access', 'name' => 'guest_menu_access']);
        // Permission::create(['module' => 'account_menu', 'title' => 'Account menu Access', 'name' => 'account_menu_access']);
        // Permission::create(['module' => 'management_access', 'title' => 'Management Access', 'name' => 'management_access']);
        // Permission::create(['module' => 'ledger_access', 'title' => 'Ledger Access', 'name' => 'ledger_access']);
    }
}
