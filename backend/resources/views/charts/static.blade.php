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
                        12 AUG 2024
                    </div>
                </td>
                <td class="text-right border-none col-4">
                    <h5 class="reds">sdfsdf</h5>
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
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Check In</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Continue</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Day use</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Comp Room</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Check Out</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Closing</td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
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
                        <div style="float: right;  width:50%;padding-top: 10px; padding-bottom:10px; padding-right:15px;"
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
                                        style="font-size: 10px; padding: 2px;">Jasmin Hall
                                    </td>
                                    <td class="text-right border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">1</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Thalam Hall
                                    </td>
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

            <td class="text-center">
                <fieldset class="card">
                    <legend>Occupied</legend>
                    <div
                        style="width: 135px; margin: auto; padding-top: 10px; padding-bottom: 10px; padding-right: 2px; text-align: center;">
                        <img style="width: 100%;  margin: auto;" src="../public/charts/donutChart.png"
                            alt="Hotel Logo" class="logo" />
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
                    <div class="card-inner-container">
                        <div style="height: 300px;width:300px;"
                            style="float: left; width:40%; padding-top: 10px; padding-bottom:10px; padding-right:5px;">
                            <img style="width:100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right; width:60%; padding-top: 10px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                <tr>
                                    <th class="text-left">Detail</th>
                                    <th class="text-right">Room rent</th>
                                    <th class="text-right">Hall rent</th>
                                    <th class="text-right">CL Recv</th>
                                    <th class="text-right">Advance</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Cash</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        2500.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        4500.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Card</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        15000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        250.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        15000.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">UPI</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        10.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        2010.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Bank</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        100.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        100.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Online</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        7250.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        250.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        8500.00</td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Total</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        7250.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        250.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        0.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1000.00</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        8500.00</td>
                                </tr>
                            </table>
                        </div>
                        <div style="clear: both;"></div>

                    </div>
                </fieldset>
            </td>
            <td class="text-center">
                <fieldset class="card" style="min-height: 145px">
                    <legend>Expenses</legend>
                    <div class="card-inner-container">
                        <div style="height: 300px;width:300px;"
                            style="float: left; width:30%; padding-top: 20px; padding-bottom:10px; padding-right:10px;">
                            <img style="width:100%;" src="../public/charts/donutChart.png" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right; width:70%; padding-top: 10px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                <tr>
                                    <th class="text-left">Category</th>
                                    <th class="text-right">Detail</th>
                                    <th class="text-right">Mode</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Food</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Provision For Kitchen
                                    </td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Cash</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        10.00
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Misc</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Masking Tape

                                    </td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Card</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        250.00

                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Food</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Tea

                                    </td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Cash</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        500,00


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Food</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Food purchase for R 105


                                    </td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        Credit</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        1600.00
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="text-left border-bottom small-font"
                                        style="font-size: 10px; padding: 2px;">Total</td>
                                    <td
                                        class="text-right border-bottom small-font"style="font-size: 10px; padding: 2px;">
                                        7250.00</td>
                                </tr>

                            </table>
                        </div>
                        <div style="clear: both;"></div>

                    </div>
                </fieldset>
            </td>

            {{-- <td class="text-center" style="width: 20%">
                <fieldset class="card">
                    <legend>Guest</legend>
                    <div class="card-inner-container">
                        <div style="width: 100%" class="text-center">
                            <img src="../public/charts/donutChart.png" alt="Hotel Logo" class="logo" />
                        </div>
                    </div>
                </fieldset>
            </td> --}}
        </tr>
    </table>
    <br />
    <br>
    <table border="1">

        <tbody>
            <tr style="background: #858585;color:white;">
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
    <br>
    <table>
        <tr>
            <td style="height:30px;width: 25%;color:black;background:green;" class="text-center">Walk 10</td>
            <td style="height:30px;width: 15%;color:black;background:rgb(119, 228, 119);" class="text-center">Travel 8
            </td>
            <td style="height:30px;width: 8%;color:black;background:orange;" class="text-center">Corp 6
            </td>
            <td style="height:30px;width: 7%;color:black;background:rgb(0, 217, 255);" class="text-center">OTA 6
            </td>
            <td style="height:30px;width: 40%;color:black;background:grey;" class="text-center">Walk 10</td>
        </tr>
    </table>
</body>

</html>
