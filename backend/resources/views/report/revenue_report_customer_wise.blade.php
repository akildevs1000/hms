<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Revenue Report - Customer wise</title>
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
        .th {
           text-align: center;
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

        .fnt-size {
            font-size: 10px
        }

        th.fnt-size {
            font-size: 10px
        }

        .text-center
        {
            text-align: center;
        }
        .text-right
        {
            text-align: right;
        }
        .grand_total
        {


            font-size: 15px;
            font-weight:bold;
        }
        .grand_total td{

            border:1px solid black!important;


        }
    </style>
</head>
<body>

<div class="row">
        <div class="col-4">
            <p class="header-txt">{{ strtoupper($company->name)}}</p>
            <span class="header-txt-span">The {{ explode(' - ',$company->company_code)[0]  }}</span>
        </div>
        <div class="col-4" style="margin: 0px">

        @if (env("APP_ENV")=="production")
        <img src="{{urldecode($company->logo)}}" height="100px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
                    @elseif   ($company->id == 1)
                <img src="{{ getcwd() . '/upload/app-logo.jpg' }}" height="70px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @elseif ($company->id == 2)
                <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="100px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
         @endif




        </div>
        <div class="col-4 header-txt-address" style="text-align:right">
            <div style="text-align:left; margin-left:70px" class="header-txt-address">
                <small> GST No: {{ $company->mol_id ?? '---' }} </small><br>

                <small> Telephone No: {{ $company->contact->number ?? '---' }} </small><br>
                <small> Email: {{ $company->user->email ?? '---' }}</small><br>
                <small> Date: {{ date('Y/m/d') }}</small>
            </div>
        </div>
    </div>

    <hr>


    <div class="row">

        <div class="col-12" style="margin: 0px;text-align:center">
            Revenue Customer wise Report {{$request->filter_from_date}} to {{$request->filter_to_date}}
        </div>

    </div>
    </div>

    <table class="mt-3 w-100">
        <tr style="background-color: white; color: black" class="my-0 py-0">
            <th class="my-0 py-0 fnt-size  "style="text-align:center"># </th>
            <th class="my-0 py-0 fnt-size" style="text-align:center">Name</th>
            <th class="my-0 py-0 fnt-size" style="text-align:center">Phone Number</th>
            <th class="my-0 py-0 fnt-size" style="text-align:center">Visits</th>
            <th class="my-0 py-0 fnt-size" style="text-align:center">Rooms</th>
            <th class="my-0 py-0 fnt-size" style="text-align:center">Booking Amount</th>
        </tr>
        @php
            $i = 1;

            $total_rooms=0;
        $total_visits=0;
        $total_price=0;
        @endphp


        @foreach ($data['data'] as $item)
        @php
        $total_visits=$total_visits+$item['number_of_visits'];
         $total_rooms+=  count(explode(',',$item['rooms']));

         $total_price+= $item['customer_total_price'];
         @endphp


            <tr style="font-size:11px">
                <td class="my-1 py-1 fnt-size ">{{ $i++ }}</td>
                <td class="my-1 py-1 fnt-size">{{ $item['title'] ?? '---' }}</td>
                <td class="my-1 py-1 fnt-size text-right">{{ $item['customer']['contact_no'] ?? '---' }}</td>
                <td class="my-1 py-1 fnt-size text-right">{{ $item['number_of_visits'] ?? '---' }}</td>
                <td class="my-1 py-1 fnt-size text-right">{{ $item['rooms'] ?? '---' }}</td>
                <td class="my-1 py-1 fnt-size text-right">{{ numFormat($item['customer_total_price']) ?? '---' }}</td>
            </tr>
        @endforeach
        <tr  class="grand_total">
                <td class="my-1 py-1 fnt-size text-right " colspan="8" style="border-left:0px!important;border-right:0px!important" >&nbsp; </td>
            </tr>
        <tr  class="grand_total">
                <td class="my-1 py-1 fnt-size text-right " colspan="3" style="border-top:1px solid red;">Total</td>

                <td class="my-1 py-1 fnt-size text-right">{{ $total_visits    }}</td>
                <td class="my-1 py-1 fnt-size text-right">{{ $total_rooms    }}</td>
                <td class="my-1 py-1 fnt-size text-right">{{numFormat( $total_price)    }}</td>
            </tr>
    </table>


    @php
        function numFormat($n = null)
        {
            if (!$n) {
                return '---';
            }
            return number_format($n, 2) ?? '---';
        }
    @endphp
</body>

</html>
