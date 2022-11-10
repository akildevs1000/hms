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
                    @if (env('APP_ENV') !== 'local')
                        <img src="{{ $company->logo }}" height="70px" width="70">
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="text-align: center; border :none;">
                <div>
                    <h2>Daily Missing</h2>
                </div>
            </td>
        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left; width:120px;"><b>Date: </b>{{ $req->daily_date }}</td>
            <td style="text-align: left; width:120px;"><b>Department: </b>{{ $info->department }}</td>
            <td style="text-align: left; width:120px; color:#f34100"><b>Missing</b>: {{ $info->total_missing }}</td>
        </tr>
    </table>
    @php $i = 0; @endphp
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px; background-color: #A6A6A6">
            <td style="text-align: left;"><b>#</b></td>
            <td style="text-align: left;"><b>EID</b></td>
            <td style="text-align: left;"><b>Name</b></td>
            <td style="text-align: left;"><b>Dept</b></td>
            <td style="text-align: left;"><b>Status </b></td>
            <td style="text-align: left;"><b>In </b></td>
            <td style="text-align: left;"><b>Out </b></td>
            <td style="text-align: left;"><b>Total Hours </b></td>
            <td style="text-align: left;"><b>Device In </b></td>
            <td style="text-align: left;"><b>Device Out </b></td>
        </tr>
        <tbody>
            @foreach ($datas as $data)
                <tr style="text-align: left; border :1px solid black; width:120px;">
                    <td style="text-align: left;"> {{ ++$i }}</td>
                    <td style="text-align: left;"> {{ $data->employee_id ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->employee->first_name ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->employee->department->name ?? '---' }}</td>
                    <td style="text-align: left; color:#f34100">
                        {{ $data->status == '---' ? 'M' : $data->status }}</td>
                    <td style="text-align: left;"> {{ $data->in ?? '---' }}</td>
                    <td style="text-align: left; "> {{ $data->out ?? '---' }}</td>
                    <td style="text-align: left; "> {{ $data->total_hrs ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->device_in->short_name ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->device_out->short_name ?? '---' }}</td>
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
