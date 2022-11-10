{{-- @php
phpinfo();
die();
@endphp --}}
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>



    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            border: none;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #eeeeee;
            text-align: left;
        }

        tr:nth-child(even) {
            /* background-color: #eeeeee; */
            border: 1px solid #eeeeee;
        }

        th {
            font-size: 9px;

        }

        td {
            font-size: 7px;
        }

        footer {
            bottom: 0px;
            position: absolute;
            width: 100%;
        }

        /* .page-break {
            page-break-after: always;
        } */

        .main-table {
            padding-bottom: 20px;
            padding-top: 10px;
            padding-right: 15px;
            padding-left: 15px;
        }

        hr {
            position: relative;
            border: none;
            height: 2px;
            background: #c5c2c2;
            padding: 0px
        }

        .title-font {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 14px;
            font-weight: bold
        }

        .summary-header th {
            font-size: 10px
        }

        .summary-table td {
            font-size: 9px
        }
    </style>
</head>
@php
    $totPresent = [];
    $totAbsent = [];
    $totMissing = [];
@endphp

<body>
    <table style="margin-top: -20px !important;backgroundd-color:blue;padding-bottom:0px ">
        <tr>
            <td style="text-align: left;width: 300px; border :none; padding:15px;   backgrozund-color: red">
                <div style=";">
                    <br> <br> <br>
                    @if (env('APP_ENV') !== 'local')
                        <img src="{{ $company->logo }}" height="70px" width="200">
                    @else
                        <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="70px" width="200">
                    @endif

                    <table style="text-align: right; border :none; width:200px; margin-top:5px;baczkground-color:blue">
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: right; border :none;font-size:10px">
                                <b>
                                    {{ 'Akkil Security & Alarm System LLC' }}
                                    {{-- <>{{ $company->name ?? 'Akkil Security & Alarm System LLC' }} --}}
                                </b>
                                <br>
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: right; border :none;font-size:10px">
                                <span style="margin-right: 3px">{{ 'P.O. Box 83481, Dubai' }}</span>
                                <br>
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: right; border :none;font-size:10px">
                                <span style="margin-right: 3px">{{ 'United Arab Emirates' }}</span>
                                <br>
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: right; border :none;font-size:10px">
                                <span style="margin-right: 3px">{{ 'Tel: +97143939562' }}</span>
                                <br>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="text-align: left;width: 333px; border :none; padding:15px; backgrozusnd-color:blue">
                <div>
                    <table style="text-align: left; border :none;  ">
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: center; border :none">
                                <span class="title-font">
                                    {{ 'Daily Attendance Summary Report' }}
                                </span>
                                <hr style="width: 230px">
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: center; border :none">
                                <span style="font-size: 11px">
                                    {{ '01 Jan 2022' }}
                                </span>
                                <hr style="width: 230px">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="text-align: right;width: 300px; border :none; backgrounsd-color: red">
                <table class="summary-table"
                    style="border:none; padding:0px 50px; margin-left:35px;margin-top:20px;margin-bottom:0px">
                    <tr style="border: none">
                        <th style="text-align: center; border :none;padding:10px;font-size: 12px " colspan="3">
                            <hr style="width: 200px">
                            Total Number of Employees : 80
                        </th>
                    </tr>
                    <tr class="summary-header" style="border: none;background-color:#eeeeee">
                        <th style="text-align: center; border :none; padding:5px">Present</th>
                        <th style="text-align: center; border :none">Absent</th>
                        <th style="text-align: center; border :none">Leave</th>
                    </tr>
                    <tr style="border: none">
                        <td style="text-align: center; border :none; padding:5px;color:green">75</td>
                        <td style="text-align: center; border :none;color:red">12</td>
                        <td style="text-align: center; border :none;color:red">3</td>
                    </tr>
                    <tr class="summary-header" style="border: none;background-color:#eeeeee ">
                        <th style="text-align: center; border :none; padding:5px">Late</th>
                        <th style="text-align: center; border :none">Early</th>
                        <th style="text-align: center; border :none">Missing</th>
                    </tr>
                    <tr style="border: none">
                        <td style="text-align: center; border :none; padding:5px;color:red">10</td>
                        <td style="text-align: center; border :none;color:green">6</td>
                        <td style="text-align: center; border :none;color:orange">2</td>
                    </tr>
                    <tr style="border: none">
                        <th style="text-align: center; border :none" colspan="3">
                            <hr style="width: 200px">
                        </th>
                    </tr>
                </table>
                <br>
            </td>
            </td>
        </tr>
    </table>
    <hr style="margin:0px;padding:0">

    @php
        $dep = ['Ifix', 'Warehouse', 'Bff Head Office', 'Zawaya Walk', 'Vbrand', 'Silicon', 'Magnify'];
        $eid = ['553', '270', '274', '199', '583', '618', '584', '282', '501', '23', '582', '290'];
        $name = ['Joan Tabinas', 'Shinoos', 'Faisal', 'Zawaya Walk', 'Sufail', 'Najeeb', 'Arshad'];
        $in = ['14:07', '15:59', '13:55', '01:56', '13:42', '11:02', '21:42'];
        $out = ['14:07', '15:59', '13:55', '01:56', '13:42', '11:02', '21:42'];
        $total_hours = ['14:07', '15:59', '13:55', '01:56', '13:42', '11:02', '21:42'];
        $ot = ['14:07', '15:59', '13:55', '01:56', '13:42', '11:02', '21:42'];

        $device_out = ['CMD', 'AFD', 'BMD', 'MOD', 'RKMD', 'MBD', 'RKMD'];
        $device_in = ['CMD', 'AFD', 'BMD', 'MOD', 'RKMD', 'MBD', 'RKMD'];
        $total_hours = ['14:07', '15:59', '13:55', '01:56', '13:42', '11:02', '21:42'];
        $status = ['A', 'P', '---'];

        $statusColor = '';
        // $i = 0;
    @endphp
    <table class="main-table">
        <tr style="text-align: left;font-weight:bold">
            <td style="text-align:  center;"> Name </td>
            <td style="text-align:  center;width:80px"> EID </td>
            <td style="text-align:  center;width:80px"> In </td>
            <td style="text-align:  center;width:80px"> Out </td>
            <td style="text-align:  center;width:80px"> Total Hours </td>
            <td style="text-align:  center;width:80px"> OT </td>
            <td style="text-align:  center;width:80px"> Status </td>
            <td style="text-align:  center;width:150px"> Device In </td>
            <td style="text-align:  center;width:150px"> Device Out </td>
        </tr>
        @for ($i = 1; $i <= 59; $i++)
            @php
                $resStatus = $status[array_rand($status, 1)];
                if ($resStatus == 'P') {
                    $totPresent[] = $i;
                    $statusColor = 'green';
                } elseif ($resStatus == 'A') {
                    $totAbsent[] = $i;
                    $statusColor = 'red';
                } elseif ($resStatus == '---') {
                    $totMissing[] = $i;
                    $statusColor = '#f34100ed';
                }

            @endphp
            <tbody>

                <tr style="text-align:  center; ">
                    <td style="text-align:  center; width:120px"> {{ $name[array_rand($name, 1)] ?? '---' }}</td>
                    <td style="text-align:  center;">{{ $eid[array_rand($eid, 1)] ?? '---' }}</td>
                    <td style="text-align:  center;"> {{ $in[array_rand($in, 1)] ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $out[array_rand($out, 1)] ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $total_hours[array_rand($total_hours, 1)] ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $ot[array_rand($ot, 1)] ?? '---' }} </td>
                    <td style="text-align:  center; color:{{ $statusColor }}">
                        {{ $resStatus }}
                    </td>
                    <td style="text-align:  center;"> {{ $device_in[array_rand($device_in, 1)] ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $device_out[array_rand($device_out, 1)] ?? '---' }} </td>
                </tr>
            </tbody>
        @endfor
        <hr style=" bottom: 0px; position: absolute; width: 100%; margin-bottom:40px">
        <footer style="padding-top: 100px!important">
            <table class="main-table">
                <tr style="border :none">
                    <td style="text-align: left;border :none"><b>Device</b>: Main Entrance = MED, Back Entrance = BED
                    </td>
                    <td style="text-align: left;border :none"><b>Shift Type</b>: Manual = MA, Auto = AU, NO = NO</td>
                    <td style="text-align: left;border :none"><b>Shift</b>: Morning = Mor, Evening = Eve, Evening2 =
                        Eve2
                    </td>
                    <td style="text-align: right;border :none;">
                        <b>Powered by</b>: <span style="color:blue"> www.ideahrms.com</span>
                    </td>
                    <td style="text-align: right;border :none">
                        Printed on : {{ date('d-M-Y ') }}
                    </td>
                </tr>
            </table>
        </footer>
    </table>

    <div id="chart">

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            series: [44, 55, 13, 43, 22],
            chart: {
                width: 380,
                type: 'pie',
            },
            labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
</body>

</html>
