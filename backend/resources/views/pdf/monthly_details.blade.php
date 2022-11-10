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
            background-color: #eeeeee;
        }

        th {
            font-size: 9px;

        }

        td {
            font-size: 7px;
        }

        footer {
            width: 100%;
            position: fixed;
            bottom: 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <table style="margin-top: -20px !important;">
        <tr style="background-color: #5fafa3;">
            <td style="text-align: left; border :none; padding:15px;">
                <div>
                    <h3 style="color: #ffffff">CHIPTRONICS SOLUTIONS</h3>
                    <h4 style="color: #ffffff">Street Address,City, State, Zip Code</h4>
                </div>

            </td>
            <td style="text-align: right; border :none;">
                <div>
                    <img width="150" src="https://placeholderlogo.com/img/placeholder-logo-5.png">
                </div>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="text-align: center; border :none;">
                <div>
                    <h2>Monthly Timesheet</h2>
                </div>
            </td>
        </tr>
    </table>

    @foreach ($data as $key => $row)
        <table style="margin-top: 5px !important;">
            <tr style="text-align: left; border :1px solid black; width:120px;">
                <td style="text-align: left;"><b>Name</b>: {{ $row['Name'] }}</td>
                <td style="text-align: left;"><b>EID</b>: {{ $row['E.ID'] }}</td>
                <td style="text-align: left;"><b>Dept</b>: {{ $row['Name'] }}</td>
                <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 30 Sep 22</td>
                <td style="text-align: left;"><b>Total Hrs</b>: {{ $row['Total Hrs'] }}</td>
                <td style="text-align: left;"><b>OT</b>: {{ $row['OT'] }}</td>
                <td style="text-align: left; color: green;"><b>Present</b>: {{ $row['Present'] }}</td>
                <td style="text-align: left; color: red;"><b>Absent</b>: {{ $row['Absent'] }}</td>
                <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: {{ $row['Late In'] }}</td>
                <td style="text-align: left; color: rgb(209, 139, 9);"><b>Early Out</b>: {{ $row['Early Out'] }}</td>
            </tr>
        </table>
        <table style="margin-top: 5px !important;">
            @foreach ($row['record'] as $key => $date)
                @if ($loop->iteration == 1)
                    <tr style="background-color: #A6A6A6;">
                        <td><b>Dates</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ substr($key, 0, 2) }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 2)
                    <tr>
                        <td><b>Days</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['day'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 3)
                    <tr>
                        <td><b>in</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['in'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 3)
                    <tr>
                        <td><b>out</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['out'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 4)
                    <tr>
                        <td><b>total_hrs</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['total_hrs'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 5)
                    <tr>
                        <td><b>ot</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['ot'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 6)
                    <tr>
                        <td><b>late_coming</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['late_coming'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 7)
                    <tr>
                        <td><b>early_going</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['early_going'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 8)
                    <tr>
                        <td><b>shift_type_id</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['shift_type_id'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 9)
                    <tr>
                        <td><b>shift_id</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['shift_id'] }} </td>
                        @endforeach
                    </tr>
                @endif

                @if ($loop->iteration == 10)
                    <tr>
                        <td><b>device_id_in</b></td>
                        @foreach ($row['record'] as $key => $date)
                            <td style="text-align: center;"> {{ $date[0]['device_id_in'] }} </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach



        </table>
    @endforeach

    <footer>
        <table>
            <tr>
                <td style="text-align: left;"><b>Device</b>: Main Entrance = MED, Back Entrance = BED</td>
                <td style="text-align: left;"><b>Shift Type</b>: Manual = MA, Auto = AU, NO = NO</td>
                <td style="text-align: left;"><b>Shift</b>: Morning = Mor, Evening = Eve, Evening2 = Eve2</td>
                <td style="text-align: right;">
                    Date : {{ date('d/M/Y H:i:s') }}
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>
