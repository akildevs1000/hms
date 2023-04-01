<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Income</title>
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


        ,
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

        .total-font-size {
            font-family: Arial, Helvetica, sans-serif
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
            @if ($company->id == 1)
                <img src="{{ getcwd() . '/upload/app-logo.jpg' }}" height="70px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @elseif ($company->id == 2)
                <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="100px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @endif
        </div>
        <div class="col-4 header-txt-address" style="text-align:right">
            <div style="text-align:left; margin-left:70px" class="header-txt-address">
                <small> <b>GST No:</b> {{ $company->mol_id ?? '33CKQPM1598B2ZZ' }} </small><br>
                {{-- 04542-291888 --}}
                <small> <b>Telephone No:</b> {{ $company->contact->number ?? '04542291888' }} </small><br>
                <small> <b> Email:</b> {{ $company->user->email ?? '---' }}</small><br>
                <small> <b> Date Range:</b> {{ $request->from }} - {{ $request->to }}</small><br>
                <small> <b>Date:</b> {{ date('Y/m/d') }}</small>
            </div>
        </div>
    </div>
    <hr>


    <div class="row">
        <div class="col-5">
        </div>
        <div class="col-4" style="margin: 0px">
            Income Report
        </div>
        <div class="col-4 header-txt-address" style="text-align:right">
        </div>
    </div>
    </div>

    <div class="row" style="border: 1px solid rgb(190, 185, 185)">
        <div class="col-3 mr-1" style="color: green">
            <span class="total-font-size">Income :
                <b>{{ number_format($totalIncomes['Cash'], 2) }}</b>
            </span>
        </div>

        <div class="col-2 mr-1" style="color: orange">
            <span class="total-font-size">Expense :
                <b>{{ number_format($accounts['expense']['OverallTotal'], 2) }}</b>
            </span>
        </div>

        <div class="col-2 mr-1" style="color: blue">
            <span class="total-font-size">Profit : <b>{{ number_format($accounts['profit'], 2) }}</b></span>
        </div>

        <div class="col-2 mr-1" style="color: red">
            <span class="total-font-size">Loss : <b>{{ number_format($accounts['loss'], 2) }}</b></span>
        </div>

        <div class="col-2 mr-1" style="color: rgb(219, 27, 162)">
            <span class="total-font-size">Ledger :
                <b>{{ number_format($totalIncomes['City_ledger'], 2) }}</b></span>
        </div>

    </div>

    <table class="mt-3 w-100">
        <tr>
            <th class="my-0 py-0 fnt-size"> # </th>
            <th class="my-0 py-0 fnt-size"> Date </th>
            <th class="my-0 py-0 fnt-size"> Time </th>
            <th class="my-0 py-0 fnt-size"> Resr/No </th>
            <th class="my-0 py-0 fnt-size"> Type </th>
            <th class="my-0 py-0 fnt-size"> Rooms </th>
            <th class="my-0 py-0 fnt-size"> Description </th>
            <th class="my-0 py-0 fnt-size"> Cash </th>
            <th class="my-0 py-0 fnt-size"> Card </th>
            <th class="my-0 py-0 fnt-size"> Online </th>
            <th class="my-0 py-0 fnt-size"> Bank </th>
            <th class="my-0 py-0 fnt-size"> UPI </th>
            <th class="my-0 py-0 fnt-size"> Cheque </th>
            <th class="my-0 py-0 fnt-size"> City Ledger </th>
        </tr>

        @foreach ($data as $index => $item)
            <tr>
                <td class="my-1 py-1 fnt-size">{{ ++$index }}</td>
                <td class="my-1 py-1 fnt-size">{{ date('d-m-Y', strtotime($item['created_at'])) }}</td>
                <td class="my-1 py-1 fnt-size">{{ $item['time'] }}</td>
                <td class="my-1 py-1 fnt-size">{{ $item['booking']['reservation_no'] }}</td>
                <td class="my-1 py-1 fnt-size">{{ $item['type'] }}</td>
                <td class="my-1 py-1 fnt-size">{{ $item['room'] }}</td>
                <td class="my-1 py-1 fnt-size">{{ $item['description'] }}</td>
                @for ($i = 1; $i <= 7; $i++)
                    <td class="text-right my-1 py-1 fnt-size">
                        @if ($item['paymentMode']['name'] == 'Cash' && $i == 1)
                            {{ $item['amount'] }}
                        @elseif($item['paymentMode']['name'] == 'Bank' && $i == 4)
                            {{ $item['amount'] }}
                        @elseif($item['paymentMode']['name'] == 'Online' && $i == 3)
                            {{ $item['amount'] }}
                        @elseif($item['paymentMode']['name'] == 'UPI' && $i == 5)
                            {{ $item['amount'] }}
                        @elseif($item['paymentMode']['name'] == 'Card' && $i == 2)
                            {{ $item['amount'] }}
                        @elseif($item['paymentMode']['name'] == 'Cheque' && $i == 6)
                            {{ $item['amount'] }}
                        @elseif($item['paymentMode']['name'] == 'City Ledger' && $i == 7)
                            {{ $item['amount'] }}
                        @else
                            ---
                        @endif
                    </td>
                @endfor
            </tr>
        @endforeach
        <tr class="text-right">
            <th colspan="7">Total</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['Cash'] }}</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['Card'] }}</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['Online'] }}</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['Bank'] }}</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['UPI'] }}</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['Cheque'] }}</th>
            <th class="my-0 py-0 fnt-size"> {{ $totalIncomes['City_ledger'] }}</th>
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
