<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Expense;
use App\Models\Leave;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Nette\Schema\Expect;

class CountController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $id = $request->company_id ?? 0;
        $model = Room::query();
        $model->whereCompanyId($id);

        return [
            [
                "title" => "Available Room",
                "value" => $model->clone()->where("status", 0)->count(),
                "icon" => "fas fa-check",
                "color" => "available",
                "link"  => "/calendar"

            ],
            [
                "title" => "Booked Room",
                "value" => $model->clone()->where("status", 1)->count(),
                "icon" => "fas fa-bookmark",
                "color" => "booked",
                "link"  => "/calendar"
            ],

            [
                "title" => "Checked In",
                "value" => $model->clone()->where("status", 2)->count(),
                "icon" => "	fas fa-user",
                "color" => "checkedIn",
                "link"  => "/customer/list"
            ],

            [
                "title" => "Checked Out",
                "value" => $model->clone()->where("status", 3)->count(),
                "icon" => "	fas fa-user",
                "color" => "checkedOut",
                "link"  => "/customer/list"

            ],
            [
                "title" => "Dirty",
                "value" => $model->clone()->where("status", 4)->count(),
                "icon" => "	fas fa-user",
                "color" => "dirty",
                "link"  => "/customer/list"

            ],
            [
                "title" => "Maintenance",
                "value" => $model->clone()->where("status", 5)->count(),
                "icon" => "	fas fa-user",
                "color" => "grey",
                "link"  => "/customer/list"

            ],
            // [
            //     "title" => "Today Earning",
            //     "value" => rand(10, 50),
            //     "icon" => "	fas fa-user",
            //     "color" => "blue",
            //     "link"  => "/"

            // ],

            [
                "title" => "Today Expense",
                "value" => number_format(Expense::whereCompanyId($id)->whereDate('created_at', date('Y-m-d'))->sum("amount"), 2),
                "icon" => "fa fa-bookmark",
                "color" => "l-bg-orange-dark",
                "link"  => "/expense"

            ],


        ];
    }

    public function employeeDashboard(Request $request)
    {

        $id = $request->company_id ?? 0;
        $employee_id = $request->employee_id ?? 0;
        $daysInMonth = Carbon::now()->month(date('m'))->daysInMonth;
        $query = Attendance::whereCompanyId($id)->where('employee_id', $employee_id);

        return [
            [
                "title" => "Total Presents",
                "value" => $query->where('status', 'p')->count(),
                "icon" => "mdi-check",
                "color" => "l-bg-green-dark",
                "border_color" => "4B7BEC",
            ],
            [
                "title" => "Total Presents Current Month",
                "value" => $query->whereMonth('date', date('m'))->where('status', 'p')->count() . '/' . $daysInMonth,
                "icon" => "mdi-history",
                "color" => "orange darken-3",
                "border_color" => "EF6C00",
            ],
            [
                "title" => "Absence",
                "value" => $query->where('status', 'a')->count(),
                "icon" => "mdi-cancel",
                "color" => "error",
                "border_color" => "DD2C00",
            ],
            [
                "title" => "Total Absence Current Month",
                "value" => $query->whereMonth('date', date('m'))->where('status', 'a')->count() . '/' . $daysInMonth,
                "icon" => "mdi-history",
                "color" => "orange darken-3",
                "border_color" => "EF6C00",
            ],
            [
                "title" => "Number of Apply Leaves",
                "value" => Leave::where('company_id', $request->company_id)->where('employee_id', $request->employee_id)->count(),
                "icon" => "mdi-home",
                "color" => "blue-grey darken-1",
                "border_color" => "526C78",
            ],
            [
                "title" => "Late Coming",
                "value" => Device::whereCompanyId($id)->count(),
                "icon" => "mdi-clock",
                "color" => "error",
                "border_color" => "DD2C00",
            ],
        ];
    }
}
