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

        .inv-table tr:nth-child(even) {
            background-color: #c1d4e2;
            border: 1px solid #eeeeee;
        }

        th {
            font-size: 9px;

        }

        td {
            font-size: 7px;
        }

        footer {
            bottom: 0px;
            position: absolute;
            width: 100%;
        }

        /* .page-break {
                page-break-after: always;
            } */

        .main-table {
            padding-bottom: 20px;
            padding-top: 10px;
            padding-right: 15px;
            padding-left: 15px;
        }

        hr {
            position: relative;
            border: none;
            height: 2px;
            background: #c5c2c2;
            padding: 0px
        }

        .title-font {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 14px;
            font-weight: bold
        }

        .summary-header th {
            font-size: 10px
        }

        .summary-table td {
            font-size: 9px
        }

        @page {
            margin-bottom: 0px !important;
            background-color: yellow
        }

        @page {
            margin: 0px;
        }

        body {
            margin: 15px;
        }

        .txt-inv {
            text-align: center;
            font-size: 11px;
            padding: 5px 10px
        }

        .txt-inv-amount {
            text-align: right;
            font-size: 11px;
            padding: 5px 10px
        }

        .txt-inv-header {
            text-align: center;
            font-size: 11px;
            padding: 5px 10px
        }

        .fooder-thank-txt {
            bottom: 20px;
            position: absolute;
            width: 97%;
            margin-bottom: 40px;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif !important;
            background-color: #c5c2c2;
            padding: 10px 0px
        }

        .tot-txt {
            text-align: left;
            font-size: 11px;
            padding: 5px 10px
        }

        .tot-txt-amount {
            text-align: right;
            font-size: 11px;
            padding: 5px 10px
        }
    </style>
</head>

<body>
    <table style="margin-top: -20px !important;background-color:bluse;padding-bottom:0px ">
        <tr>
            <td style="text-align: left;width: 200px; border :none; padding:15px;   backgrozund-color: rded">
                <div style=";">
                    <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="70px" width="100">
                </div>
            </td>
            <td style="text-align: left;width: 200px; border :none; padding:15px; backgrozusnd-color:dblue">
                <div>
                    <table style="text-align: left; border :none;  ">
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: center; border :none">
                                <span class="title-font">
                                    Tax Invoice
                                </span>
                                <hr style="width: 230px">
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: center; border :none">
                                <span style="font-size: 9px">
                                    <b> Booking ID</b> : 121112020 <br> <b>Meal Plan</b> : Room With BreakFast
                                </span>
                                <hr style="width: 230px">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="text-align: right;width: 250px; border :none; background-color: rded">
                <table class="summary-table"
                    style="border:none; padding:0px 10px; margin-left:0px;margin-top:20px;margin-bottom:0px">
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <b>
                                HYDER PARK - THE LUXURY HOTEL
                            </b>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 0px"> 298/A2, M.M ROAD, KODAIKANAL </span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 0px"><b>www.hyderspark.com</b></span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 0px">hyderskodai@gmail.com</span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 0px"><b>GSTIN:33CKQPM1598B2AA</b></span>
                            <br>
                        </td>
                    </tr>
                </table>
                <br>
            </td>
            </td>
        </tr>
    </table>




    <table style="margin-top: 5px !important;background-color:#c1d4e2;padding-bottom:0px">
        <tr>
            <td style="border :none;width: 300px; background-color:rded">
                <div>
                    <table style="border:none;width: 300px;font-size:15px;padding-left:5px">
                        <tr style="text-align: left; border :none;background-color:bdlue">
                            <td style="text-align: left; border :none;font-size:10px;width: 70px">Guest Name &nbsp;:
                            </td>
                            <th style="text-align: left; border :none;font-size:10px;">Fahath</th>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: left; border :none;font-size:10px">Address &nbsp; &nbsp; &nbsp;
                                &nbsp; :
                            </td>
                            <td style="text-align: left; border :none;font-size:10px;">
                                1/41 ashok nagar, Guidy, Chennai. Tamil Naadu.India
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: left; border :none;font-size:10px;">
                                Mobile
                                <span style="margin-left: 29px">:</span>
                            </td>
                            <th style="text-align: left; border :none;font-size:10px;">0752388923</th>
                        </tr>
                    </table>
                </div>
            </td>

            <td style="text-align: right;width: 300px; border :none; background-color: rdgb(146, 55, 55)">
                <div style="font-size:10px;text-align: right;">
                    <table style="width:250px;background-color:redd;margin-left:130px;margin-top:5px">
                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> Bill No
                            </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px">01454
                            </td>
                        </tr>
                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> Bill
                                Date </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:20px; padding:0px 5px text-align:right;border:none;font-size:10px">
                                {{ date('d/m/y') }}</td>
                        </tr>
                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> Check
                                In </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:20px; padding:0px 5px text-align:right;border:none;font-size:10px">
                                {{ now() }}</td>
                        </tr>
                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> Check
                                Out </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:20px; padding:0px 5px text-align:right;border:none;font-size:10px">
                                {{ now() }}</td>
                        </tr>

                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> Check
                                Out </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:20px; padding:0px 5px text-align:right;border:none;font-size:10px">
                                {{ now() }}</td>
                        </tr>

                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> No of
                                Days </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:20px; padding:0px 5px text-align:right;border:none;font-size:10px">
                                4</td>
                        </tr>
                        <tr style="text-align: right;border:none;">
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> No of
                                Pax </td>
                            <td style="width:13px; padding:0px 5px text-align:right;border:none;font-size:10px"> : </td>
                            <td style="width:20px; padding:0px 5px text-align:right;border:none;font-size:10px">
                                2</td>
                        </tr>
                    </table>
                </div>
                <br>
            </td>

        </tr>
    </table>


    <div style="padding: 10px 0px ">
        <table>
            <tr>
                <td>Booking ID : 121112020</td>
                <td style="text-align: right">Meal Plan : Room With BreakFast</td>
            </tr>
        </table>
    </div>

    <hr style="margin:0px;padding:0">
    @php

    @endphp
    <table class="inv-table">
        <tr style="background-color: rgb(19, 19, 75);color:white">
            <th style="padding:10px">Date</th>
            <th class="txt-inv-header">Room No</th>
            <th class="txt-inv-header">Description</th>
            <th class="txt-inv-header">Amount</th>
            <th class="txt-inv-header">After Discount</th>
            <th class="txt-inv-header">SGST</th>
            <th class="txt-inv-header">CGST</th>
            <th class="txt-inv-header">Total Rs</th>
        </tr>
        <tbody>
            @for ($i = 1; $i <= 10; $i++)
                <tr>
                    <td class="txt-inv">{{ $i }}/11/22</td>
                    <td class="txt-inv" style="width:10px">{{ $i == 5 ? '3' : $i }}01</td>
                    <td class="txt-inv" style="width:200px">Room tariff</td>
                    <td class="txt-inv-amount">
                        4500 <br>
                        (-500)
                    </td>
                    <td class="txt-inv-amount">4000.00</td>
                    <td class="txt-inv-amount">
                        4500 <br>
                        (9.00%)
                    </td>
                    <td class="txt-inv-amount">
                        4500 <br>
                        (9.00%)
                    </td>
                    <td class="txt-inv-amount">4720.00</td>
                </tr>
            @endfor
        </tbody>
    </table>
    <table>
        <tr>
            <td style="text-align: left;width: 300px; border :none;width:400px;font-size:12px">
                <p>Payment Mode : <b>CASH</b></p>
                <p>Total Rs : Twelve Thousand Eigth Hundred Fourty Eidgh Only</p>
            </td>
            <td style="width:200px;border :none;text-align: left">
                <table style="background-color: rgb(19, 19, 75);color:white">
                    <tr>
                        <td class="tot-txt">Total IRS (excl GST)</td>
                        <td class="tot-txt-amount">522</td>
                    </tr>
                    <tr>
                        <td class="tot-txt">SGST</td>
                        <td class="tot-txt-amount">522</td>
                    </tr>
                    <tr>
                        <td class="tot-txt">CGST</td>
                        <td class="tot-txt-amount">522</td>
                    </tr>
                    <tr>
                        <th class="tot-txt">Total Amount Incl.GST IRS</th>
                        <td class="tot-txt-amount">12848.00</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <h4 class="fooder-thank-txt">
        THANK YOU !! VISIT AGAIN !!
    </h4>
    <hr style=" bottom: 0px; position: absolute; width: 97%; margin-bottom:40px">
    <footer style="padding-top: 100px!important">
        <p style="text-align: center;font-size:11px">This Is System Generated Invoice And Does Not Require Signature
        </p>
    </footer>


</body>

</html>
