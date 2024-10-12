<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ChartController extends Controller
{
    public function index()
    {
        // Example data
        $data = [
            'labels' => ['January', 'February', 'March'],
            'values' => [10, 25, 15],
        ];

        return response()->json($data);
    }

    public function callView()
    {
        $pdf = PDF::loadView("charts.index")->stream('chart_report.pdf');
        return $pdf;
    }
}
