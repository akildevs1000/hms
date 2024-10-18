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

        .page-break {
            /* page-break-after: always; */
            page-break-before: always !important;
        }



        .footer {
            position: fixed;
            bottom: 2mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-size: 12px;
            text-align: right;
            margin-top: 20px;
        }

        .page-number:after {
            content: counter(page);
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

        .small-font {
            font-size: 11px
        }

        legend {
            padding-top: 2px
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
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
            width: 100px;
            height: auto;
        }

        .card {
            border: 1px solid #ddd;
            color: #959595;
            min-height: 50px;
            border-radius: 15px;
            text-align: left;
        }

        .card-inner-container {
            padding: 0px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table>
            <tr>
                <td class="text-left">
                    <div class="logo pt">
                        <img src="https://backend.ezhms.com/upload/app-logo.jpg" alt="Hotel Logo" class="logo" />
                    </div>
                </td>
                <td class="text-center uppercase">
                    <div>NIGHT AUDIT REPORT</div>
                    <div class="border-top border-bottom" style="margin: auto; width: 100px">
                        {{ date('d M Y') }}
                    </div>
                </td>
                <td class="text-right">
                    <h5>sdfsdfdsfsdf</h5>
                    <div style="line-height: 1">
                        P.O.Box: <small>sdfsdf</small>
                    </div>
                    <div style="line-height: 1">
                        <small>sdfsdfsd</small>
                    </div>
                    <div style="line-height: 1">
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
                            <img style="width:100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right;  width:50%;padding-top: 10px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                @foreach ($data['room'] as $roomValue)
                                    <tr>
                                        <td class="text-left border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">
                                            <div
                                                style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                            </div>

                                            <span style="margin-top: 1px">
                                                {{ $roomValue['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-right border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}</td>

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
                    <legend>Food</legend>

                    <div class="card-inner-container">
                        <div style="float: left; width:50%;padding-top: 10px; padding-bottom:10px; padding-right:2px;">
                            <img style="width:100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right;  width:50%;padding-top: 30px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                @foreach ($data['guest'] as $roomValue)
                                    <tr>
                                        <td class="text-left border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">
                                            <div
                                                style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                            </div>

                                            <span style="margin-top: 1px">
                                                {{ $roomValue['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-right border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}</td>

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
                            <img style="width:100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                class="logo" />
                        </div>
                        <div style="float: right;  width:50%;padding-top: 30px; padding-bottom:10px; padding-right:15px;"
                            class="">
                            <table>
                                @foreach ($data['guest'] as $roomValue)
                                    <tr>
                                        <td class="text-left border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">
                                            <div
                                                style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                            </div>

                                            <span style="margin-top: 1px">
                                                {{ $roomValue['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-right border-bottom small-font"
                                            style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}</td>

                                    </tr>
                                @endforeach
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
                                <img style="width: 100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['income'] as $roomValue)
                                        @if (!isset($roomValue['hide']))
                                            <tr>
                                                <td class="text-left border-bottom small-font"
                                                    style="font-size: 10px; padding: 2px;">
                                                    <div
                                                        style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                                    </div>

                                                    <span style="margin-top: 1px">
                                                        {{ $roomValue['label'] }}
                                                    </span>
                                                </td>
                                                <td class="text-right border-bottom small-font"
                                                    style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}
                                                </td>
                                            </tr>
                                        @endif
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
                                <img style="width: 100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['expense'] as $roomValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">
                                                <div
                                                    style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                                </div>

                                                <span style="margin-top: 1px">
                                                    {{ $roomValue['label'] }}
                                                </span>
                                            </td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}</td>

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
                                <img style="width: 100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['managementExpense'] as $roomValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">
                                                <div
                                                    style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                                </div>

                                                <span style="margin-top: 1px">
                                                    {{ $roomValue['label'] }}
                                                </span>
                                            </td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}</td>

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
                                <img style="width: 100%;" src="{{ asset('charts/donutChart.png') }}" alt="Hotel Logo"
                                    class="logo" />
                            </td>
                            <td style="width: 50%; vertical-align: middle; padding: 10px;">
                                <table style="width: 100%;">
                                    @foreach ($data['profit_loss'] as $roomValue)
                                        <tr>
                                            <td class="text-left border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">
                                                <div
                                                    style="background:{{ $roomValue['color'] }}; width: 5px; height: 5px; display: inline-block;">
                                                </div>

                                                <span style="margin-top: 1px">
                                                    {{ $roomValue['label'] }}
                                                </span>
                                            </td>
                                            <td class="text-right border-bottom small-font"
                                                style="font-size: 10px; padding: 2px;">{{ $roomValue['value'] }}</td>

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
            <tr style="background: rgb(224, 224, 224);color:white;">
                @foreach ($data['balance_sheet']['labels'] as $label)
                    <td class="text-center" style="color: black">{{ $label }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($data['balance_sheet']['values'] as $value)
                    <td style="color: {{ $value['color'] ?? 'black' }}" class="text-center">{{ $value['value'] }}
                    </td>
                @endforeach
            </tr>

            <tr>
                @foreach ($data['balance_sheet']['totals'] as $value)
                    <td colspan="{{ $value['colspan'] ?? '1' }}" style="color: {{ $value['color'] ?? 'black' }}"
                        class="text-center">{{ $value['value'] }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>

</html>
