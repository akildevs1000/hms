<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
        * {
            font-family: "Source Sans Pro", sans-serif !important;
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #858585
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
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0 15px 0;
            border-radius: 8px;
            background-color: #f9f9f9;
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

        .card {
            border: 1px solid #ddd;
            color: #959595;
            min-height: 50px;
            border-radius: 15px;
            text-align: left;
        }

        .small-font {
            font-size: 11px
        }

        legend {
            padding-top: 2px
        }

        .card-inner-container {
            padding: 0px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="">
            <tr>
                <td class="text-left border-none col-4">
                    <div class="logo pt">
                        <img src="https://backend.ezhms.com/upload/app-logo.jpg" alt="Hotel Logo" class="logo" />
                    </div>
                </td>
                <td class="text-center border-none col-4 uppercase">
                    <div>NIGHT AUDIT REPORT</div>
                    <div class="border-top border-bottom" style="margin: auto; width: 100px">
                        {{ date('d M Y') }}
                    </div>
                </td>
                <td class="text-right border-none col-4">
                    <h5 class="reds">sdfsdfdsfsdf</h5>
                    <div class="greens" style="line-height: 1">
                        P.O.Box: <small>sdfsdf</small>
                    </div>
                    <div class="greens" style="line-height: 1">
                        <small>sdfsdfsd</small>
                    </div>
                    <div class="greens" style="line-height: 1">
                        <small>sdfdsfsdf</small>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br><br>
    <table>
        <tr>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Room</legend>
                    <div class="card-inner-container">
                        <div style="float: left; width:50%;padding-top: 10px; padding-bottom:10px; padding-right:2px;">
                            <img style="width:100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right;  width:50%;padding-top: 10px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                @foreach ($data['room'] as $roomName => $roomValue)
                                    <tr>
                                        <td class="text-left border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">{{ ucfirst($roomName) }}</td>
                                        <td class="text-right border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">{{ $roomValue }} </td>

                                    </tr>
                                @endforeach

                            </table>
                        </div>
                        <div style="clear: both;"></div>

                    </div>
                </fieldset>
            </td>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Guest</legend>

                    <div class="card-inner-container">
                        <div style="float: left; width:50%;padding-top: 10px; padding-bottom:10px; padding-right:2px;">
                            <img style="width:100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right;  width:50%;padding-top: 30px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                @foreach ($data['guest'] as $mealName => $mealValue)
                                    <tr>
                                        <td class="text-left border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">
                                            {{ ucfirst($mealName) }} <!-- Capitalize the meal name -->
                                        </td>
                                        <td class="text-right border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">
                                            {{ $mealValue }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div style="clear: both;"></div>

                    </div>
                </fieldset>
            </td>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Staff</legend>
                    <div class="card-inner-container">
                        <div style="float: left; width:50%;padding-top: 10px; padding-bottom:10px; padding-right:2px;">
                            <img style="width:100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right;  width:50%;padding-top: 30px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Break fast
                                    </td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Lunch</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Dinner</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>

                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Total</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                            </table>
                        </div>
                        <div style="clear: both;"></div>

                    </div>
                </fieldset>
            </td>



        </tr>
    </table>

    <br />
    <br />
    <table>

        <tr>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Income</legend>
                    <table style="height: 160px; width: 100%;">
                        <tr style="height: 100%;">
                            <td style="width: 50%; vertical-align: middle; text-align: center;">
                                <img style="width: 100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['income'] as $incomeKey => $incomeValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeKey }}</td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeValue }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Expenses</legend>
                    <table style="height: 160px; width: 100%;">
                        <tr style="height: 100%;">
                            <td style="width: 50%; vertical-align: middle; text-align: center;">
                                <img style="width: 100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['expense'] as $incomeKey => $incomeValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeKey }}</td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeValue }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Management Expenses
                    </legend>
                    <table style="height: 160px; width: 100%;">
                        <tr style="height: 100%;">
                            <td style="width: 50%; vertical-align: middle; text-align: center;">
                                <img style="width: 100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['managementExpense'] as $incomeKey => $incomeValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeKey }}</td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeValue }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            <td class="text-center">
                <fieldset class="card">
                    <legend>Profit and Loss </legend>
                    <table style="height: 140px; width: 100%;padding-top:20px;">
                        <tr style="height: 100%;">
                            <td style="width: 50%; vertical-align: middle; text-align: center;">
                                <img style="width: 100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['profit_loss'] as $incomeKey => $incomeValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeKey }}</td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $incomeValue }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>
    <br />
    <br>
    <table>
        <tr class="red">
            @foreach ($data['calculateBookingsBySource'] as $expenseKey => $expenseValue)
                <td style="padding:5px 0px;height:20px;min-width:50px;width:{{ $expenseValue['percentage'] }};color:black;background:{{ $expenseValue['color'] }};"
                    class="text-center">{{ $expenseValue['label'] }} {{ $expenseValue['value'] }}</td>
            @endforeach
        </tr>
    </table>
    <br>
    <table border="1">

        <tbody>
            <tr style="background: grey;color:white;">
                <td class="text-center" style="color: black">Income ( cash )
                </td>
                <td class="text-center" style="color: black">Income ( others )
                </td>
                <td class="text-center" style="color: black">Expenses ( cash )
                </td>
                <td class="text-center" style="color: black">Expanses ( Others )
                </td>
                <td class="text-center" style="color: black">Cash in Hand
                </td>
            </tr>
            <tr>
                <td style="color: black" class="text-center">2500.00
                </td>
                <td class="text-center" style="color: black">7500.00</td>
                <td class="text-center" style="color: black">750.00
                </td>
                <td style="color: black" class="text-center">500.00
                </td>
                <td style="color: green" class="text-center">1750.00
                </td>
            </tr>
            <tr>
                <td colspan="2" style="color: black" class="text-center">2500.00
                </td>

                <td colspan="2" style="color: black" class="text-center">500.00
                </td>
                <td style="color: green" class="text-center">1750.00
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
