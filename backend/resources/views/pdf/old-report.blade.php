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
            border-collapse: collapse;
            border: none;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #eeeeee;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #eeeeee;
        }

        th {
            font-size: 12px;

        }

        td {
            font-size: 11px;
        }

        h4,
        td,
        th {
            text-align: center;
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
    <table style="margin-top: 0px !important;">
        <tr style="background-color: #5fafa3;">
            <td style="text-align: left; border :none;">
                <div>
                    <b style="color: #ffffff">CHIPTRONICS SOLUTIONS</b>
                </div>
                <div>
                    <p style="color: #ffffff">Street Address,City, State, Zip Code</p>
                </div>
            </td>
            <td style="text-align: right; border :none;">
                <div>
                    <img width="150" src="https://placeholderlogo.com/img/placeholder-logo-5.png">
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 5px !important;">
        <tr>
            <td style="text-align: left; border :none;">
                <div>
                    <h2>Monthly Timesheet</h2>
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 5px !important;">
        <tr>
            <td style="text-align: left; border :none; width:120px;">
                <div>
                    <b>Report Type</b>
                </div>
                <div>
                    Monthly
                </div>
            </td>
            <td style="text-align: left; border :none; width:120px;">
                <div>
                    <b>Name</b>
                </div>
                <div>
                    Francis Gill
                </div>
            </td>
            <td style="text-align: left; border :none;">
                <div>
                    <b>Regular Hours Per Day</b>
                </div>
                <div>
                    7.5 hours
                </div>
            </td>

        </tr>
    </table>

    <table style="margin-top: 15px !important;">
        <tr>
            <th>Date</th>
            <th>E.ID</th>
            <th>Shift</th>
            <th>Shift Type</th>
            <th>Time Table</th>
            <th>Status</th>
            <th>In</th>
            <th>Out</th>
            <th>Total Hrs</th>
            <th>Late Coming</th>
            <th>Early Going</th>
            <th>D.In</th>
            <th>D.Out</th>
            <th>D.Out</th>
            <th>D.Out</th>
        </tr>

        @for ($i = 1; $i <= 2; $i++)
            @foreach ($data as $item)
                <tr>
                    <td> {{ $item['date'] }} </td>
                    <td> {{ $item['employee_id'] }} </td>
                    <td> {{ $item['shift_id'] }} </td>
                    <td> {{ $item['shift_type_id'] }} </td>
                    <td> {{ $item['time_table_id'] }} </td>
                    <td> {{ $item['status'] }} </td>
                    {{-- <td>
                {!! $item['status'] == 'A'
                    ? 'red'
                    : '<img width="15" src="https://th.bing.com/th/id/OIP.m3G72SBuXkG1-9gj4oeGtgHaGx?pid=ImgDet&rs=1">' !!} </td> --}}
                    <td> {{ $item['in'] }} </td>
                    <td> {{ $item['out'] }} </td>
                    <td> {{ $item['total_hrs'] }} </td>
                    <td> {{ $item['late_coming'] }} </td>
                    <td> {{ $item['early_going'] }} </td>
                    <td> {{ $item['device_id_in'] }} </td>
                    <td> {{ $item['device_id_out'] }} </td>
                    <td> {{ $item['device_id_out'] }} </td>
                    <td> {{ $item['device_id_out'] }} </td>

                </tr>
            @endforeach
            @foreach ($data as $item)
                <tr>
                    <td> {{ $item['date'] }} </td>
                    <td> {{ $item['employee_id'] }} </td>
                    <td> {{ $item['shift_id'] }} </td>
                    <td> {{ $item['shift_type_id'] }} </td>
                    <td> {{ $item['time_table_id'] }} </td>
                    <td> {{ $item['status'] }} </td>
                    {{-- <td>
                {!! $item['status'] == 'A'
                    ? 'red'
                    : '<img width="15" src="https://th.bing.com/th/id/OIP.m3G72SBuXkG1-9gj4oeGtgHaGx?pid=ImgDet&rs=1">' !!} </td> --}}
                    <td> {{ $item['in'] }} </td>
                    <td> {{ $item['out'] }} </td>
                    <td> {{ $item['total_hrs'] }} </td>
                    <td> {{ $item['late_coming'] }} </td>
                    <td> {{ $item['early_going'] }} </td>
                    <td> {{ $item['device_id_in'] }} </td>
                    <td> {{ $item['device_id_out'] }} </td>
                    <td> {{ $item['device_id_out'] }} </td>
                    <td> {{ $item['device_id_out'] }} </td>

                </tr>
            @endforeach
        @endfor

    </table>
    <footer>
        <table>
            <tr>
                <td style="text-align: right; border :none;">
                    Date : {{ date('d/M/Y H:i:s') }}
                </td>

            </tr>
        </table>
    </footer>
</body>

</html>
