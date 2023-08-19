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

        //new
        Permission::create(['module' => 'calendar', 'title' => 'Calendar Access', 'name' => 'calendar_access']);
        Permission::create(['module' => 'calendar', 'title' => 'Calendar View', 'name' => 'calendar_view']);
        Permission::create(['module' => 'calendar', 'title' => 'Calendar Create', 'name' => 'calendar_create']);
        Permission::create(['module' => 'calendar', 'title' => 'Calendar Edit', 'name' => 'calendar_edit']);
        Permission::create(['module' => 'calendar', 'title' => 'Calendar Delete', 'name' => 'calendar_delete']);

        Permission::create(['module' => 'history_reservation', 'title' => 'Reservation Access', 'name' => 'reservation_access']);
        Permission::create(['module' => 'history_reservation', 'title' => 'Reservation View', 'name' => 'reservation_view']);
        Permission::create(['module' => 'history_reservation', 'title' => 'Reservation Create', 'name' => 'reservation_create']);
        Permission::create(['module' => 'history_reservation', 'title' => 'Reservation Edit', 'name' => 'reservation_edit']);
        Permission::create(['module' => 'history_reservation', 'title' => 'Reservation Delete', 'name' => 'reservation_delete']);

        Permission::create(['module' => 'history_in_house', 'title' => 'In-House Access', 'name' => 'in_house_access']);
        Permission::create(['module' => 'history_in_house', 'title' => 'In-House View', 'name' => 'in_house_view']);
        Permission::create(['module' => 'history_in_house', 'title' => 'In-House Create', 'name' => 'in_house_create']);
        Permission::create(['module' => 'history_in_house', 'title' => 'In-House Edit', 'name' => 'in_house_edit']);
        Permission::create(['module' => 'history_in_house', 'title' => 'In-House Delete', 'name' => 'in_house_delete']);

        Permission::create(['module' => 'history_checkout', 'title' => 'Checkout Access', 'name' => 'checkout_access']);
        Permission::create(['module' => 'history_checkout', 'title' => 'Checkout View', 'name' => 'checkout_view']);
        Permission::create(['module' => 'history_checkout', 'title' => 'Checkout Create', 'name' => 'checkout_create']);
        Permission::create(['module' => 'history_checkout', 'title' => 'Checkout Edit', 'name' => 'checkout_edit']);
        Permission::create(['module' => 'history_checkout', 'title' => 'Checkout Delete', 'name' => 'checkout_delete']);

        Permission::create(['module' => 'guest', 'title' => 'Guest Access', 'name' => 'guest_access']);
        Permission::create(['module' => 'guest', 'title' => 'Guest View', 'name' => 'guest_view']);
        Permission::create(['module' => 'guest', 'title' => 'Guest Create', 'name' => 'guest_create']);
        Permission::create(['module' => 'guest', 'title' => 'Guest Edit', 'name' => 'guest_edit']);
        Permission::create(['module' => 'guest', 'title' => 'Guest Delete', 'name' => 'guest_delete']);

        Permission::create(['module' => 'source', 'title' => 'Source Access', 'name' => 'source_access']);
        Permission::create(['module' => 'source', 'title' => 'Source View', 'name' => 'source_view']);
        Permission::create(['module' => 'source', 'title' => 'Source Create', 'name' => 'source_create']);
        Permission::create(['module' => 'source', 'title' => 'Source Edit', 'name' => 'source_edit']);
        Permission::create(['module' => 'source', 'title' => 'Source Delete', 'name' => 'source_delete']);

        Permission::create(['module' => 'inquiry', 'title' => 'Inquiry Access', 'name' => 'inquiry_access']);
        Permission::create(['module' => 'inquiry', 'title' => 'Inquiry View', 'name' => 'inquiry_view']);
        Permission::create(['module' => 'inquiry', 'title' => 'Inquiry Create', 'name' => 'inquiry_create']);
        Permission::create(['module' => 'inquiry', 'title' => 'Inquiry Edit', 'name' => 'inquiry_edit']);
        Permission::create(['module' => 'inquiry', 'title' => 'Inquiry Delete', 'name' => 'inquiry_delete']);

        Permission::create(['module' => 'accounts_posting', 'title' => 'Accounts Posting Access', 'name' => 'accounts_posting_access']);
        Permission::create(['module' => 'accounts_posting', 'title' => 'Accounts Posting View', 'name' => 'accounts_posting_view']);
        Permission::create(['module' => 'accounts_posting', 'title' => 'Accounts Posting Create', 'name' => 'accounts_posting_create']);
        Permission::create(['module' => 'accounts_posting', 'title' => 'Accounts Posting Edit', 'name' => 'accounts_posting_edit']);
        Permission::create(['module' => 'accounts_posting', 'title' => 'Accounts Posting Delete', 'name' => 'accounts_posting_delete']);

        Permission::create(['module' => 'accounts_expences', 'title' => 'Accounts Expences Access', 'name' => 'accounts_expences_access']);
        Permission::create(['module' => 'accounts_expences', 'title' => 'Accounts Expences View', 'name' => 'accounts_expences_view']);
        Permission::create(['module' => 'accounts_expences', 'title' => 'Accounts Expences Create', 'name' => 'accounts_expences_create']);
        Permission::create(['module' => 'accounts_expences', 'title' => 'Accounts Expences Edit', 'name' => 'accounts_expences_edit']);
        Permission::create(['module' => 'accounts_expences', 'title' => 'Accounts Expences Delete', 'name' => 'accounts_expences_delete']);

        Permission::create(['module' => 'accounts_gst_bills', 'title' => 'Accounts GST Bills  Access', 'name' => 'accounts_gst_access']);
        Permission::create(['module' => 'accounts_gst_bills', 'title' => 'Accounts GST Bills  View', 'name' => 'accounts_gst_view']);
        Permission::create(['module' => 'accounts_gst_bills', 'title' => 'Accounts GST Bills Create', 'name' => 'accounts_gst_create']);
        Permission::create(['module' => 'accounts_gst_bills', 'title' => 'Accounts GST Bills Edit', 'name' => 'accounts_gst_edit']);
        Permission::create(['module' => 'accounts_gst_bills', 'title' => 'Accounts GST Bills Delete', 'name' => 'accounts_gst_delete']);

        Permission::create(['module' => 'ledger_agents', 'title' => 'Ledger Agents Access', 'name' => 'ledger_agents_access']);
        Permission::create(['module' => 'ledger_agents', 'title' => 'Ledger Agents View', 'name' => 'ledger_agents_view']);
        Permission::create(['module' => 'ledger_agents', 'title' => 'Ledger Agents Create', 'name' => 'ledger_agents_create']);
        Permission::create(['module' => 'ledger_agents', 'title' => 'Ledger Agents Edit', 'name' => 'ledger_agents_edit']);
        Permission::create(['module' => 'ledger_agents', 'title' => 'Ledger Agents Delete', 'name' => 'ledger_agents_delete']);

        Permission::create(['module' => 'ledger_guests', 'title' => 'Ledger Guests Access', 'name' => 'ledger_guests_access']);
        Permission::create(['module' => 'ledger_guests', 'title' => 'Ledger Guests View', 'name' => 'ledger_guests_view']);
        Permission::create(['module' => 'ledger_guests', 'title' => 'Ledger Guests Create', 'name' => 'ledger_guests_create']);
        Permission::create(['module' => 'ledger_guests', 'title' => 'Ledger Guests Edit', 'name' => 'ledger_guests_edit']);
        Permission::create(['module' => 'ledger_guests', 'title' => 'Ledger Guests Delete', 'name' => 'ledger_guests_delete']);

        Permission::create(['module' => 'night_audit', 'title' => 'Night Audit Access', 'name' => 'night_audit_access']);
        Permission::create(['module' => 'night_audit', 'title' => 'Night Audit View', 'name' => 'night_audit_view']);
        Permission::create(['module' => 'night_audit', 'title' => 'Night Audit Create', 'name' => 'night_audit_create']);
        Permission::create(['module' => 'night_audit', 'title' => 'Night Audit Edit', 'name' => 'night_audit_edit']);
        Permission::create(['module' => 'night_audit', 'title' => 'Night Audit Delete', 'name' => 'night_audit_delete']);

        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Lost and Found Items Access', 'name' => 'lost_and_found_access']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Lost and Found Items View', 'name' => 'lost_and_found_view']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Lost and Found Items Create', 'name' => 'lost_and_found_create']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Lost and Found Items Edit', 'name' => 'lost_and_found_edit']);
        Permission::create(['module' => 'lost_and_found_items', 'title' => 'Lost and Found Items Delete', 'name' => 'lost_and_found_delete']);

        Permission::create(['module' => 'management_expenses', 'title' => 'Expenses Access', 'name' => 'management_expenses_access']);
        Permission::create(['module' => 'management_expenses', 'title' => 'Expenses View', 'name' => 'management_expenses_view']);
        Permission::create(['module' => 'management_expenses', 'title' => 'Expenses Create', 'name' => 'management_expenses_create']);
        Permission::create(['module' => 'management_expenses', 'title' => 'Expenses Edit', 'name' => 'management_expenses_edit']);
        Permission::create(['module' => 'management_expenses', 'title' => 'Expenses Delete', 'name' => 'management_expenses_delete']);

        Permission::create(['module' => 'management_income', 'title' => 'Income Access', 'name' => 'management_income_access']);
        Permission::create(['module' => 'management_income', 'title' => 'Income View', 'name' => 'management_income_view']);
        Permission::create(['module' => 'management_income', 'title' => 'Income Create', 'name' => 'management_income_create']);
        Permission::create(['module' => 'management_income', 'title' => 'Income Edit', 'name' => 'management_income_edit']);
        Permission::create(['module' => 'management_income', 'title' => 'Income Delete', 'name' => 'management_income_delete']);

        Permission::create(['module' => 'management_payments', 'title' => 'Payments Access', 'name' => 'management_payments_access']);
        Permission::create(['module' => 'management_payments', 'title' => 'Payments View', 'name' => 'management_payments_view']);
        Permission::create(['module' => 'management_payments', 'title' => 'Payments Create', 'name' => 'management_payments_create']);
        Permission::create(['module' => 'management_payments', 'title' => 'Payments Edit', 'name' => 'management_payments_edit']);
        Permission::create(['module' => 'management_payments', 'title' => 'Payments Delete', 'name' => 'management_payments_delete']);

        Permission::create(['module' => 'management_summary', 'title' => 'Summary Access', 'name' => 'management_summary_access']);
        Permission::create(['module' => 'management_summary', 'title' => 'Summary View', 'name' => 'management_summary_view']);
        Permission::create(['module' => 'management_summary', 'title' => 'Summary Create', 'name' => 'management_summary_create']);
        Permission::create(['module' => 'management_summary', 'title' => 'Summary Edit', 'name' => 'management_summary_edit']);
        Permission::create(['module' => 'management_summary', 'title' => 'Summary Delete', 'name' => 'management_summary_delete']);

        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Access', 'name' => 'management_soldout_access']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut View', 'name' => 'management_soldout_view']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Create', 'name' => 'management_soldout_create']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Edit', 'name' => 'management_soldout_edit']);
        Permission::create(['module' => 'management_soldout', 'title' => 'SoldOut Delete', 'name' => 'management_soldout_delete']);

        Permission::create(['module' => 'management_revenue_report', 'title' => 'Revenue Report Access', 'name' => 'management_revenue_report_access']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Revenue Report View', 'name' => 'management_revenue_report_view']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Revenue Report Create', 'name' => 'management_revenue_report_create']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Revenue Report Edit', 'name' => 'management_revenue_report_edit']);
        Permission::create(['module' => 'management_revenue_report', 'title' => 'Revenue Report Delete', 'name' => 'management_revenue_report_delete']);

        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Revenue Report Access', 'name' => 'management_top_10_customers_access']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Revenue Report View', 'name' => 'management_top_10_customers_view']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Revenue Report Create', 'name' => 'management_top_10_customers_create']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Revenue Report Edit', 'name' => 'management_top_10_customers_edit']);
        Permission::create(['module' => 'management_top_10_customers_report', 'title' => 'Revenue Report Delete', 'name' => 'management_top_10_customers_delete']);

        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Revenue Report Access', 'name' => 'management_custom_soldout_access']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Revenue Report View', 'name' => 'management_custom_soldout_view']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Revenue Report Create', 'name' => 'management_custom_soldout_create']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Revenue Report Edit', 'name' => 'management_custom_soldout_edit']);
        Permission::create(['module' => 'management_custom_soldout_report', 'title' => 'Revenue Report Delete', 'name' => 'management_custom_soldout_delete']);

        Permission::create(['module' => 'settings_rooms_category', 'title' => 'Settings Room Category Access', 'name' => 'settings_rooms_category_access']);
        Permission::create(['module' => 'settings_rooms_category', 'title' => 'Settings Room Category View', 'name' => 'settings_rooms_category_view']);
        Permission::create(['module' => 'settings_rooms_category', 'title' => 'Settings Room Category Create', 'name' => 'settings_rooms_category_create']);
        Permission::create(['module' => 'settings_rooms_category', 'title' => 'Settings Room Category Edit', 'name' => 'settings_rooms_category_edit']);
        Permission::create(['module' => 'settings_rooms_category', 'title' => 'Settings Room Category Delete', 'name' => 'settings_rooms_category_delete']);

        Permission::create(['module' => 'settings_rooms', 'title' => 'Settings Room  Access', 'name' => 'settings_rooms_access']);
        Permission::create(['module' => 'settings_rooms', 'title' => 'Settings Room  View', 'name' => 'settings_rooms_view']);
        Permission::create(['module' => 'settings_rooms', 'title' => 'Settings Room  Create', 'name' => 'settings_rooms_create']);
        Permission::create(['module' => 'settings_rooms', 'title' => 'Settings Room  Edit', 'name' => 'settings_rooms_edit']);
        Permission::create(['module' => 'settings_rooms', 'title' => 'Settings Room  Delete', 'name' => 'settings_rooms_delete']);

        Permission::create(['module' => 'settings_room_price', 'title' => 'Settings Room Price Access', 'name' => 'settings_room_price_access']);
        Permission::create(['module' => 'settings_room_price', 'title' => 'Settings Room Price View', 'name' => 'settings_room_price_view']);
        Permission::create(['module' => 'settings_room_price', 'title' => 'Settings Room Price Create', 'name' => 'settings_room_price_create']);
        Permission::create(['module' => 'settings_room_price', 'title' => 'Settings Room Price Edit', 'name' => 'settings_room_price_edit']);
        Permission::create(['module' => 'settings_room_price', 'title' => 'Settings Room Price Delete', 'name' => 'settings_room_price_delete']);

        Permission::create(['module' => 'settings_users', 'title' => 'Settings Users Access', 'name' => 'settings_users_access']);
        Permission::create(['module' => 'settings_users', 'title' => 'Settings Users View', 'name' => 'settings_users_view']);
        Permission::create(['module' => 'settings_users', 'title' => 'Settings Users Create', 'name' => 'settings_users_create']);
        Permission::create(['module' => 'settings_users', 'title' => 'Settings Users Edit', 'name' => 'settings_users_edit']);
        Permission::create(['module' => 'settings_users', 'title' => 'Settings Users Delete', 'name' => 'settings_users_delete']);

        Permission::create(['module' => 'settings_roles', 'title' => 'Settings Roles Access', 'name' => 'settings_roles_access']);
        Permission::create(['module' => 'settings_roles', 'title' => 'Settings Roles View', 'name' => 'settings_roles_view']);
        Permission::create(['module' => 'settings_roles', 'title' => 'Settings Roles Create', 'name' => 'settings_roles_create']);
        Permission::create(['module' => 'settings_roles', 'title' => 'Settings Roles Edit', 'name' => 'settings_roles_edit']);
        Permission::create(['module' => 'settings_roles', 'title' => 'Settings Roles Delete', 'name' => 'settings_roles_delete']);

        Permission::create(['module' => 'settings_permissions', 'title' => 'Settings Permissions Access', 'name' => 'settings_permissions_access']);
        Permission::create(['module' => 'settings_permissions', 'title' => 'Settings Permissions View', 'name' => 'settings_permissions_view']);
        Permission::create(['module' => 'settings_permissions', 'title' => 'Settings Permissions Create', 'name' => 'settings_permissions_create']);
        Permission::create(['module' => 'settings_permissions', 'title' => 'Settings Permissions Edit', 'name' => 'settings_permissions_edit']);
        Permission::create(['module' => 'settings_permissions', 'title' => 'Settings Permissions Delete', 'name' => 'settings_permissions_delete']);

        Permission::create(['module' => 'setting_access', 'title' => 'Settings Access', 'name' => 'setting_access']);
        Permission::create(['module' => 'history_menu', 'title' => 'History Menu', 'name' => 'history_menu']);
        Permission::create(['module' => 'guest_menu', 'title' => 'Guest menu', 'name' => 'guest_menu']);
        Permission::create(['module' => 'account_menu', 'title' => 'Account menu', 'name' => 'account_menu']);
        Permission::create(['module' => 'management_access', 'title' => 'Management Access', 'name' => 'management_access']);
    }
}
