<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap"> --}}

    <style>
        * {
            font-family: "Source Sans Pro", sans-serif !important;
            margin: 0;
            padding: 0;
            font-size: 11px;

        }

        .text-color {
            color: #8a8a8a;
        }

        body {
            margin: 25px 25px 25px 25px;
        }

        .border {
            padding: 5px;
            border: 1px solid #dddddd;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #dddddd;
            padding: 4px;
            text-align: left;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .my-blue {
            background-color: #408dfb !important;
        }

        .my-green {
            background-color: #a2b63b !important;
        }

        .my-grey {
            background-color: rgb(182, 182, 182) !important;
        }

        .my-red {
            background-color: #e04e4f !important;
        }

        .col-1 {
            width: 8.3%;
        }

        .col-2 {
            width: 16.6%;
        }

        .col-3 {
            width: 24.9%;
        }

        .col-4 {
            width: 33.2%;
        }

        .col-6 {
            width: 50%;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .border-top {
            border-top: 1px solid #dddddd;
            /* Add top border */
        }

        .border-bottom {
            border-bottom: 1px solid #dddddd;
            /* Add top border */
        }

        .border-none {
            border: none;
            /* Add top border */
        }

        .mt-1 {
            margin-top: 5px;
            /* Add top border */
        }

        .mt-2 {
            margin-top: 10px;
            /* Add top border */
        }

        .mt-3 {
            margin-top: 15px;
            /* Add top border */
        }

        .mt-4 {
            margin-top: 20px;
            /* Add top border */
        }

        .mt-5 {
            margin-top: 25px;
            /* Add top border */
        }

        .page-break {
            /* page-break-after: always; */
            page-break-before: always !important;
        }

        .circle-container {
            text-align: left;
        }

        .circle-container img {
            border-radius: 50%;
            max-width: 100%;
            vertical-align: middle;
            /* Adjust as needed for spacing */
        }

        .width-100 {
            width: 100%;
        }

        .width-50 {
            width: 50%;
        }

        .text-white {
            color: #fff;
        }

        .footer {
            position: fixed;
            bottom: 2mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-size: 12px;
        }

        .page-number:after {
            content: counter(page);
        }

        body {
            font-family: Arial, sans-serif;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 100px;
            margin-right: 15px;
        }

        .hotel-info {
            text-align: right;
            /* Align the hotel name and tagline to the right */
        }

        .guest-info {
            margin: 20px 0;
            background-color: #f7f7f7;
            padding: 15px;
            border-radius: 8px;
            width: 100%;
        }

        .guest-info p {
            margin: 0;
            padding: 2px 0;
        }

        .quotation-header {
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            margin: 15px 0 15px 0;
            border-radius: 8px;
        }

        .info-container div {
            margin: 0 10px;
        }

        .info-container .label {
            font-weight: bold;
        }

        .footer {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="footer">

        {{-- <hr class="mt-1" style="color: #dddddd" />
        <table>
            <tr>
                <td class="text-left border-none col-4">
                    <div>Report Date: 01-01-2024</div>
                </td>
                <td class="text-center border-none col-4">
                    <div>This is a system-generated report</div>
                </td>
                <td class="text-right border-none col-4">
                    Page <span class="page-number"></span>
                </td>
            </tr> --}}
        </table>
    </div>

    {{-- @include('common.company-info', $statement->company) --}}

    <div class="header">
        <table class="">
            <tr>
                <td class="text-left border-none col-4">
                    <div class="logo pt">
                        @if (env('APP_ENV') == 'production')
                            <img src="{{ urldecode($company->logo) }}" height="100px" width="100"
                                style="margin-left: 50px;margin-top: 0px">
                        @else
                            <img src="https://backend.ezhms.com/upload/app-logo.jpg" alt="Hotel Logo" class="logo" />
                        @endif

                    </div>
                    <div>
                        <h4 style="margin-bottom:2px;">To</h4>
                        <p class="text-color" style="line-height: 1.5">{{ $customer->title }} {{ $customer->full_name }}
                        </p>
                        <p class="text-color" style="line-height: 1.5">{{ $customer->country }}</p>

                        <p class="text-color" style="line-height: 1.5">TRN 100437109000003
                        </p>

                    </div>
                </td>
                <td class="text-center border-none col-4 uppercase"></td>
                <td class="text-right border-none col-4">
                    <h5 class="reds">{{ $company->name }}</h5>
                    <div class="text-color" style="line-height: 1.5">
                        P.O.Box: <small>{{ $company->p_o_box_no }}</small>
                    </div>
                    <div class="text-color" style="line-height: 1.5">
                        <small>{{ $company->location }}</small>
                    </div>
                    <div class="text-color" style="line-height: 1.5">
                        <small>{{ $company->user->email }}</small>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="text-left border-none col-4"></td>
                <td class="text-center border-none col-4 uppercase"></td>
                <td class="text-right border-none col-4">
                    <h5 class="reds">Statement of Accounts</h5>
                    <div class="text-color"
                        style="float:right;line-height: 1.5;border-top:1px solid #ddd;border-bottom:1px solid #ddd; width:150px;">
                        <small>{{ $from }} to {{ $to }}</small>
                    </div>
                    <div style="clear: both"></div>
                </td>
            </tr>




        </table>

        <table style="border-collapse: collapse; width: 30%;margin-left: auto; ">
            <tbody>
                <tr>
                    <td class="border-none" colspan="2" style="background: #8a8a8a;color: white">
                        <div style="">Account Summary</div>
                    </td>
                </tr>
                <tr>
                    <td class="border-none">


                        <div class="text-color" style="line-height: 1.5">
                            <small>Opening Balance</small>
                        </div>


                    </td>
                    <td class="border-none" style="text-align: right;">
                        <div class="text-color" style="line-height: 1.5">
                            <small>AED 3,659.25</small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="border-none">
                        <div class="text-color" style="line-height: 1.5">
                            <small>Invoiced Amount</small>
                        </div>
                    </td>

                    <td class="border-none" style="text-align: right;">
                        <div class="text-color" style="line-height: 1.5">
                            <small>AED 6,851.25</small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="border-none">
                        <div class="text-color" style="line-height: 1.5">
                            <small>Amount Received</small>
                        </div>
                    </td>
                    <td class="border-none" style="text-align: right;">
                        <div class="text-color" style="line-height: 1.5">
                            <small>AED 8,526.00</small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="border-none" style="border-top: 1px solid #ddd">
                        <div class="text-color" style="line-height: 1.5">
                            <small>Balance Due</small>
                        </div>
                    </td>
                    <td class="border-none" style="text-align: right;border-top: 1px solid #ddd">
                        <div class="text-color" style="line-height: 1.5">
                            <small>AED 1,984.50</small>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <table style="width: 100%;" cellspacing="0">
        <tr style="color:#6aa4f5;">
            <td class="text-center">#</td>
            <td class="text-center">Date</td>
            <td class="text-center">Transaction</td>
            <td class="text-center">Description</td>
            <td class="text-right">Amount</td>
            <td class="text-right">Payment</td>
            <td class="text-right">Balance</td>
        </tr>
        <tbody>
            @foreach ($statementList as $key => $item)
                <tr>
                    <td class="text-center">
                        <div class="text-color" style="line-height: 1.5">
                            <small>{{ $key + 1 }}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-color" style="line-height: 1.5">
                            <small>{{ $item['date'] }}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-color" style="line-height: 1.5">
                            <small>{{ $item['description'] }}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-color" style="line-height: 1.5">
                            @if ($item['description'] == '---')
                                ---
                            @else
                                <small>
                                    INV-{{ $item['booking_id'] < 1000 ? '000' . $item['booking_id'] : $item['booking_id'] }}
                                </small>
                            @endif

                        </div>
                    </td>

                    <td class="text-right">
                        <div class="text-color" style="line-height: 1.5">
                            <small>{{ number_format($item['booking']->total_price, 2) }}</small>
                        </div>
                    </td>
                    <td style="width:50px;" class="text-right">
                        <div class="text-color" style="line-height: 1.5">
                            <small>{{ number_format($item['booking']->paid_amounts, 2) }}</small>
                        </div>
                    </td>
                    <td style="width:50px;" class="text-right">
                        <div class="text-color" style="line-height: 1.5">
                            <small>{{ number_format($item['booking']->balance, 2) }}</small>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
