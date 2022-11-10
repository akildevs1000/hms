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
                    <h3 style="color: #ffffff">{{ $company->name ?? 'Sample Company' }}</h3>
                    <h4 style="color: #ffffff">{{ $company->location ?? 'Waleed Road Burdubai' }}</h4>
                </div>
            </td>
            <td style="text-align: right; border :none;">
                <div>
                    @php
                        $i = 0;
                        // $img = env('APP_ENV') == 'local' ? public_path() . '/upload/1665500012.png' : $company->logo;
                        $img = env('APP_ENV') == 'local' ? public_path() . '/upload/' . $company->pdf_logo : $company->logo;
                    @endphp
                    {{-- @dd(getcwd() . '/upload/1665497365.jpeg') --}}
                    <img src="{{ $img }}" height="70px" width="70">
                </div>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="text-align: center; border :none;">
                <div>
                    <h2>Daily Summary v2</h2>
                </div>
            </td>
        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left; width:120px;"><b>Date: </b>{{ $req->daily_date }}</td>
            <td style="text-align: left; width:120px;"><b>Department: </b>{{ $info->department }}</td>
            <td style="text-align: left; width:120px; color: green;"><b>Present</b>: {{ $info->total_present }}</td>
            <td style="text-align: left; width:120px; color: red;"><b>Absent</b>: {{ $info->total_absent }}</td>
            <td style="text-align: left; width:120px; color: #f34100ed;"><b>Missing</b>: {{ $info->total_missing }}</td>
        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px; background-color: #A6A6A6">
            <td style="text-align: left;"><b>#</b></td>
            <td style="text-align: left;"><b>EID</b></td>
            <td style="text-align: left;"><b>Name</b></td>
            <td style="text-align: left;"><b>Dept</b></td>
            <td style="text-align: left;"><b>Date</b></td>
            <td style="text-align: left;"><b>Status </b></td>



            {{-- "attendance_logs": [
                {
                  "id": 13898,
                  "UserID": "664",
                  "LogTime": "2022-10-11 08:40:43",
                  "DeviceID": "OX-8862021010097",
                  "SerialNumber": "8657",
                  "created_at": null,
                  "updated_at": "2022-10-11T12:50:16.000000Z",
                  "company_id": 1,
                  "branch_id": 0,
                  "checked": true,
                  "show_log_time": 1665465043,
                  "time": "08:40",
                  "date": "11-Oct-22",
                  "edit_date": "2022-10-11",
                  "hour_only": "08"
                }
              ], --}}


            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: left;"><b>Log {{ $i }} </b></td>
            @endfor

            {{-- @foreach ($data->attendance_logs as $item)
                <td style="text-align: left;"><b>Status </b></td>
            @endforeach --}}

        </tr>
        <tbody>
            @php
                $statusColor = '';
            @endphp
            @foreach ($datas as $data)
                @if ($data->status == 'P')
                    {{ $statusColor = 'green' }}
                @elseif ($data->status == 'A')
                    {{ $statusColor = 'red' }}
                @elseif ($data->status == '---')
                    {{ $statusColor = '#f34100ed' }}
                @endif

                <tr style="text-align: left; border :1px solid black; width:120px;">
                    <td style="text-align: left;"> {{ ++$i }}</td>
                    <td style="text-align: left;"> {{ $data->employee_id ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->employee->first_name ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->employee->department->name ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $req->daily_date ?? '---' }}</td>
                    <td style="text-align: left; color:{{ $statusColor }}">
                        {{ $data->status == '---' ? 'M' : $data->status }}</td>

                    {{-- @dd($data->AttendanceLogs) --}}

                    @foreach ($data->AttendanceLogs as $item)
                        <td style="text-align: left;"> {{ $item->LogTime ?? '---' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <footer>
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
    </footer> --}}
</body>

</html>
