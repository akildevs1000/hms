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
                    @else
                        <img src="{{ getcwd() . '/upload/1665668211.png' }}" height="70px" width="70">
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="text-align: center; border :none;">
                <div>
                    <h2>Daily Summary</h2>
                </div>
            </td>
        </tr>
    </table>
    {{-- <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black!important; width:120px;">
            <td style="text-align: center; width:120px;"><b>Daily Performance Report </b></td>
            <td style="text-align: right; width:10px;"><b>Date:- </b></td>
            <td style="text-align: left; width:120px"><b>Present</b>: {{ $req->daily_date }}</td>
        </tr>
        <tr>
        </tr>
        <tr style="text-align: left; border :1px solid black!important; width:120px;">
            <td colspan="3" style="text-align: center"><b> {{ $company->name ?? 'V Perfumes' }} </b></td>
        </tr>
    </table> --}}
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left; width:120px;"><b>Date: </b>{{ $req->daily_date }}</td>
            <td style="text-align: left; width:120px;"><b>Department: </b>{{ $info->department }}</td>
            <td style="text-align: left; width:120px; color: green;"><b>Present</b>: {{ $info->total_present }}</td>
            <td style="text-align: left; width:120px; color: red;"><b>Absent</b>: {{ $info->total_absent }}</td>
            <td style="text-align: left; width:120px; color: #f34100ed;"><b>Missing</b>: {{ $info->total_missing }}</td>
        </tr>
    </table>

    @php
        $statusColor = '';
        $i = 0;
    @endphp
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left;font-weight:bold">
            <td style="text-align:  left;"> Name </td>
            <td style="text-align:  center;width:80px"> EID </td>
            <td style="text-align:  center;width:80px"> In </td>
            <td style="text-align:  center;width:80px"> Out </td>
            <td style="text-align:  center;width:80px"> Total Hours </td>
            <td style="text-align:  center;width:80px"> OT </td>
            <td style="text-align:  center;width:80px"> Status </td>
            <td style="text-align:  left;width:150px"> Device In </td>
            <td style="text-align:  left;width:150px"> Device Out </td>
        </tr>
        @foreach ($datas as $data)
            @php
                if ($data->status == 'P') {
                    $statusColor = 'green';
                } elseif ($data->status == 'A') {
                    $statusColor = 'red';
                } elseif ($data->status == '---') {
                    $statusColor = '#f34100ed';
                }
            @endphp
            <tbody>
                {{-- <tr style="text-align: left;">
                    <td style="text-align: left; background-color:#A6A6A6; width:150x"><b>Department</b></td>
                    <td style="text-align: left; width:200px;  background-color: #A6A6A6">
                        {{ $data->employee->department->name ?? '---' }}</td>
                    <td colspan="7" style="text-align: left;"></td>
                </tr> --}}
                <tr style="text-align:  center; ">
                    <td style="text-align:  left; width:120px"> {{ $data->employee->first_name ?? '---' }}</td>
                    <td style="text-align:  center;">{{ $data->employee_id ?? '---' }}</td>
                    <td style="text-align:  center;"> {{ $data->in ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $data->out ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $data->total_hrs ?? '---' }} </td>
                    <td style="text-align:  center;"> {{ $data->total_hrs ?? '---' }} </td>
                    <td style="text-align:  center; color:{{ $statusColor }}""> {{ $data->status ?? '---' }} </td>
                    <td style="text-align:  left;"> {{ $data->device_in->short_name ?? '---' }} </td>
                    <td style="text-align:  left;"> {{ $data->device_out->short_name ?? '---' }} </td>
                </tr>
            </tbody>
        @endforeach
    </table>




    {{-- <table style="margin-top: 5px !important;">
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
            @php
                $statusColor = '';
                $i = 0;
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
                    <td style="text-align: left; color:{{ $statusColor }}">
                        {{ $data->status == '---' ? 'M' : $data->status }}</td>
                    <td style="text-align: left;"> {{ $data->in ?? '---' }}</td>
                    <td style="text-align: left; "> {{ $data->out ?? '---' }}</td>
                    <td style="text-align: left; "> {{ $data->total_hrs ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->device_in->short_name ?? '---' }}</td>
                    <td style="text-align: left;"> {{ $data->device_out->short_name ?? '---' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}




</body>

</html>
