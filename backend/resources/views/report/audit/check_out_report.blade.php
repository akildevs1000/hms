<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $fileName }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-primary,
        .table-primary>th,
        .table-primary>td {
            background-color: #c6e0f5;
        }

        .table-primary th,
        .table-primary td,
        .table-primary thead th,
        .table-primary tbody+tbody {
            border-color: #95b3d7;
        }

        .table-secondary,
        .table-secondary>th,
        .table-secondary>td {
            background-color: #d6d8db;
        }

        .table-secondary th,
        .table-secondary td,
        .table-secondary thead th,
        .table-secondary tbody+tbody {
            border-color: #b3b7bb;
        }

        .table-success,
        .table-success>th,
        .table-success>td {
            background-color: #c7eed8;
        }

        .table-success th,
        .table-success td,
        .table-success thead th,
        .table-success tbody+tbody {
            border-color: #98dfb6;
        }

        .m-1 {
            margin: 0.25rem;
        }

        .m-2 {
            margin: 0.5rem;
        }

        .m-3 {
            margin: 1rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mr-1 {
            margin-right: 0.25rem;
        }

        .ml-3 {
            margin-left: 1rem;
        }

        .mx-4 {
            margin-right: 1.5rem;
            margin-left: 1.5rem;
        }

        .my-5 {
            margin-top: 2.5rem;
            margin-bottom: 2.5rem;
        }

        .pr-1 {
            padding-right: 0.25rem;
        }

        .pt-2 {
            padding-top: 0.5rem;
        }

        .pl-3 {
            padding-left: 1rem;
        }

        .px-4 {
            padding-right: 1.5rem;
            padding-left: 1.5rem;
        }

        .py-5 {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }

        .row::after {
            content: "";
            clear: both;
            display: table;
        }

        .col {
            width: 5%;
            float: left;
            padding: 5px;
        }


        .col-1 {
            width: 8.33%;
            float: left;
            padding: 5px;
        }

        .col-2 {
            width: 16.66%;
            float: left;
            padding: 5px;
        }

        .col-3 {
            width: 24.99%;
            float: left;
            padding: 5px;
        }

        .col-4 {
            width: 33.32%;
            float: left;
            padding: 5px;
        }

        .col-5 {
            width: 41.65%;
            float: left;
            padding: 5px;
        }

        .col-6 {
            width: 49.98%;
            float: left;
            padding: 5px;
        }

        .col-7 {
            width: 58.31%;
            float: left;
            padding: 5px;
        }

        .col-8 {
            width: 66.64%;
            float: left;
            padding: 5px;
        }

        .col-9 {
            width: 74.97%;
            float: left;
            padding: 5px;
        }

        .col-10 {
            width: 83.3%;
            float: left;
            padding: 5px;
        }

        .col-11 {
            width: 91.63%;
            float: left;
            padding: 5px;
        }

        .col-12 {
            width: 100%;
            float: left;
            padding: 5px;
        }

        .form-input {
            width: 100%;
            padding: 2px 5px;
            border-radius: 0px;
            resize: vertical;
            outline: 0;
        }

        .label-txt {
            font-size: 14px
        }

        .w-25 {
            width: 25% !important;
        }

        .w-50 {
            width: 50% !important;
        }

        .w-75 {
            width: 75% !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .w-auto {
            width: auto !important;
        }
        input {
            /* border: none; */
            /* border-bottom: 1px solid black; */
            padding: 5px 10px;
            outline: none;
            font-size: 13px
        }

        hr {
            position: relative;
            border: none;
            height: 1px;
            background: rgb(167, 164, 164);
        }

        .terms {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif
        }

        .header-txt {
            font-size: 20px;
            font-weight: bolder;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header-txt-span {
            font-size: 12px;
            font-weight: bolder;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .header-txt-address {
            font-size: 12px;
            /* font-weight: bolder; */
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px;
            padding: 0px;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #e9e9e9;
            width: 50%;
            margin: auto;
        }

        td,
        th {
            font-size: 12px;
            text-align: left;
            padding: 2px 2px;
            border: 1px solid #e9e9e9;
        }
        .text-right
        {
            text-align:right;
        }
    </style>
</head>

<body>

    <div class="row">
        <div class="col-4">
            <p class="header-txt">HYDERS PARK</p>
            <span class="header-txt-span">{{ $company->id == 1 ? 'The Business Hotel' : 'The Luxuery Hotel' }}</span>
        </div>
        <div class="col-4" style="margin: 0px">
            {{-- @if ($company->id == 1)
                <img src="{{ getcwd() . '/upload/app-logo.jpg' }}" height="70px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @elseif ($company->id == 2)
                <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="100px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @endif --}}
        </div>
        <div class="col-4 header-txt-address" style="text-align:right">
            <div style="text-align:left; margin-left:70px" class="header-txt-address">
                <small> GST No: {{ $company->mol_id ?? '33CKQPM1598B2ZZ' }} </small><br>
                {{-- 04542-291888 --}}
                <small> Telephone No: {{ $company->contact->number ?? '04542291888' }} </small><br>
                <small> Email: {{ $company->user->email ?? '---' }}</small><br>
                <small> Date: {{ $date }}</small>
            </div>
        </div>
    </div>
    <hr>


    <div class="row">
        <div class="col-5">
        </div>
        <div class="col-4" style="margin: 0px">
        {{$fileName}}
        </div>
        <div class="col-4 header-txt-address" style="text-align:right">
        </div>
    </div>
    </div>

    <table class="mt-3 w-100">
        <tr style="background-color: white; color: black" class="my-0 py-0">
            <th class="my-0 py-0">#</th>
            <th class="my-0 py-0">Guest</th>
            <th class="my-0 py-0">Rev. No</th>
            <th class="my-0 py-0">Rooms</th>
            <th class="my-0 py-0">Source</th>
            <th class="my-0 py-0">CheckIn</th>
            <th class="my-0 py-0">CheckOut</th>
            <th class="my-0 py-0">Tariff</th>
            <th class="my-0 py-0">Advance</th>
            <th class="my-0 py-0">Cash</th>
            <th class="my-0 py-0">Card</th>
            <th class="my-0 py-0">Online</th>
            <th class="my-0 py-0">Bank</th>
            <th class="my-0 py-0">UPI</th>
            <th class="my-0 py-0">Balance</th>
            <th class="my-0 py-0">Remark</th>
        </tr>
        @php
            $i = 1;

            $functionName = $fileName;
            $getPaySumFn = 'getPaySums' . $fileName;

            $getPaySumFn = function ($payload, $mode) {
                $sum = 0;
                foreach ($payload as $e) {
                    if ($e['payment_method_id'] == $mode) {
                        $sum += floatval($e['credit']);
                    }
                }
                return number_format($sum, 2);
            };

            ${$functionName} = function ($item, $mode) use ($getPaySumFn) {
                $creditTrans = $item['transactions'];
                switch ($mode) {
                    case 1:
                        return $getPaySumFn($creditTrans, 1);
                    // return getPaySum($creditTrans, 1);
                    case 2:
                        return $getPaySumFn($creditTrans, 2);
                    // return getPaySum($creditTrans, 2);
                    case 3:
                        return $getPaySumFn($creditTrans, 3);
                    // return getPaySum($creditTrans, 3);
                    case 4:
                        return $getPaySumFn($creditTrans, 4);
                    // return getPaySum($creditTrans, 4);
                    case 5:
                        return $getPaySumFn($creditTrans, 5);
                    // return getPaySum($creditTrans, 5);
                    default:
                        return '0';
                        break;
                }
            };

            // function getPaySum($payload, $mode)
            // {
            //     $sum = 0;
            //     foreach ($payload as $e) {
            //         if ($e['payment_method_id'] == $mode) {
            //             $sum += floatval($e['credit']);
            //         }
            //     }
            //     return number_format($sum, 2);
            // }

            $cashTotal=0;
            $cardTotal=0;
            $onlineTotal=0;
            $bankTotal=0;
            $upiTotal=0;
            $balanceTotal=0;

        @endphp
        <tbody>
            @foreach ($data as $index => $item)
@php
$cashTotal+=is_numeric(${$functionName}($item, 1))?${$functionName}($item, 1):0;
$cardTotal+=is_numeric(${$functionName}($item, 2))?${$functionName}($item, 2):0;
$onlineTotal+=is_numeric(${$functionName}($item, 3))?${$functionName}($item, 3):0;
$bankTotal+=is_numeric(${$functionName}($item, 4))?${$functionName}($item,4):0;
$upiTotal+=is_numeric(${$functionName}($item, 5))?${$functionName}($item, 5):0;
$balanceTotal+=$item->balance;
@endphp
                <tr style="background-color: #FFF;">
                    <td>{{ ++$index }}</td>
                    <td>{{ $item->customer->first_name ?? '' }}</td>
                    <td> {{ $item->reservation_no }} </td>
                    <td class="room-width"> {{ $item->rooms }} </td>
                    <td>{{ $item->source ?? '' }}</td>
                    <td>{{ $item->check_in ?? '' }}</td>
                    <td>{{ $item->check_out ?? '' }}</td>
                    <td class="text-right">{{ $item->total_price }}</td>
                    <td class="text-right">{{ $item->advance_price > 0 ? $item->advance_price : 0 }}</td>
                    <td class="text-right">{{ ${$functionName}($item, 1) }}</td>
                    <td class="text-right">{{ ${$functionName}($item, 2) }}</td>
                    <td class="text-right">{{ ${$functionName}($item, 3) }}</td>
                    <td class="text-right">{{ ${$functionName}($item, 4) }}</td>
                    <td class="text-right">{{ ${$functionName}($item, 5) }}</td>
                    <td class="text-right">{{ $item->balance }}</td>
                    <td>{{ $item->balance > 0 ? 'Due' : 'Paid' }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="9" style="text-align:right">Total</td>
                <td class="text-right">{{$cashTotal}}.00</td>
                <td class="text-right">{{$cardTotal}}.00</td>
                <td class="text-right">{{$onlineTotal}}.00</td>
                <td class="text-right">{{$bankTotal}}.00</td>
                <td class="text-right">{{$upiTotal}}.00</td>
                <td class="text-right">{{$balanceTotal}}.00</td>
                <td  > </td>
            </tr>
        </tbody>
    </table>
    <br/>
    <center>  @if(count($data) == 0)
       No Records are available
@endif </center>
</body>

</html>
