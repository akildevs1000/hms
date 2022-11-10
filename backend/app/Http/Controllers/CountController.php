<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $model = Attendance::query();
        $model->whereCompanyId($id);
        $model = $model->whereDate('date', date('Y-m-d'))->get();

        return [
            [
                "title" => "Reservations",
                "value" => rand(10,50),
                "icon" => "fas fa-bed",
                "color" => "l-bg-purple-dark",
                "link"  => "/",

            ],
            [
                "title" => "Booked Room",
                "value" => rand(10,50),
                "icon" => "fas fa-bookmark",
                "color" => "l-bg-green-dark ",
                "link"  => "/"
            ],
            [
                "title" => "Available Room",
                "value" => rand(10,50),
                "icon" => "fas fa-check",
                "color" => "l-bg-orange-dark",
                "link"  => "/"

            ],
            [
                "title" => "Total Customer",
                "value" => rand(10,50),
                "icon" => "	fas fa-user",
                "color" => "l-bg-cyan-dark",
                "link"  => "/"

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
