<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Report;
use Carbon\CarbonPeriod;
use App\Models\OrderRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{

    public function getOccupancyRate(Request $request)
    {
        $reportModel = Report::query();
        $reportModel->whereCompanyId($request->company_id);

        if (($request->filled('from') && $request->from) && ($request->filled('to') && $request->to)) {
            $reportModel->WhereDate('date', '>=', $request->from);
            $reportModel->whereDate('date', '<=', $request->to);
        }

        $totalSold =  $reportModel->sum('sold');
        $totalUnsold =  $reportModel->sum('unsold');
        $data = $reportModel->paginate($request->per_page ?? 30);


        return response()->json([
            'record' => $data,
            'message'      => '',
            'status'       => true,
        ]);
    }

    public function getSingleDayOccupancyRate(Request $request)
    {
        $reportModel = Report::query();
        $data = $reportModel->whereCompanyId($request->company_id)->whereDate('date', $request->date)->first();
        return response()->json([
            'record' => $data,
            'message'      => '',
            'status'       => true,
        ]);
    }


    public function getOccupancyRateByMonth(Request $request)
    {
        $reportModel = Report::query();
        $arr = [];
        $data = $reportModel->select('sold', 'unsold', 'date')
            ->whereCompanyId($request->company_id)
            ->whereMonth('date', $request->month)
            ->get()->toArray();

        foreach ($data as $data) {
            $arr['date'][] =  $data['date'];
            $arr['sold'][] =  $data['sold'];
            $arr['unsold'][] =  $data['unsold'];
        }
        return $arr;
    }


    public function generateOccupancyRate(Request $request)
    {
        $orderRoomModel = OrderRoom::query();
        $roomModel = Room::query();
        $totalRooms =  $roomModel->whereCompanyId($request->company_id)->count();
        $period = CarbonPeriod::create($request->from, $request->to);
        foreach ($period as $date) {
            $ocuDate = $date->format('Y-m-d');
            $numberOfSoldRooms = $orderRoomModel->clone()->whereDate('date', $ocuDate)->whereCompanyId($request->company_id)->count();

            $soldRate = round(($numberOfSoldRooms / $totalRooms) * 100);
            $unsoldRate = 100 - $soldRate;

            // if ($numberOfSoldRooms) {
            Report::whereDate('date', $date)->delete();
            Report::create([
                'company_id' => $request->company_id,
                'date' => $date,
                'sold' => $soldRate,
                'unsold' => $unsoldRate,
            ]);
            // }
        }
        return response()->json(['record' => null, 'message' => '', 'status' => true,]);
    }
}