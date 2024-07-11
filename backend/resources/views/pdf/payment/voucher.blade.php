<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Voucher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            /* width: 40%; */
            margin-top: -5;
            margin-bottom: 2;
        }

        .header {
            text-align: center;
        }

        .header img {
            width: 100px;
        }

        .header h1 {
            margin: 10px 0;
            font-size: 24px;
            color: #e62e00;
        }

        .company-details {
            margin: 5px 0;
        }

        .quotation-details {
            margin-top: 20px;
        }

        .quotation-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .quotation-details th,
        .quotation-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .quotation-details th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 10px;
        }

        .text-right {
            text-align: right;
        }

        .red {
            background: red;
        }

        .table {
            width: 100%;
        }

        .w-100 {
            width: 100%;
        }

        .heading {
            font-size: 18px;
        }

        .sub-heading {
            font-size: 18px;
        }

        .image-box {
            width: 150px;
        }

        .voucher {
            margin: 0 auto;
        }

        .voucher h4 {
            text-align: center;
            margin: 0;
            padding: 10px 0;
        }

        .voucher table {
            width: 100%;
            border-collapse: collapse;
        }

        .voucher table,
        .voucher th,
        .voucher td {
            border: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="table">
            <tr>
                <td>
                    <div>
                        <div class="image-box">
                            <img class="w-100" src="https://amc.mytime2cloud.com/mail-logo.png"
                                alt="Akil Security & Alarm Systems LLC" />
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <div>
                        <div class="sub-heading">Payment Voucher</div>
                        <div><small>Exp Ref # {{ $expense->id < 1000 ? '00' : '' }}{{ $expense->id }}</small></div>
                        <div><small>Date: <span>{{ date('d-M-y') }}</span></small></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="company-details">
                        <div class="sub-heading">Akil Security & Alarm Systems LLC</div>
                        <div>
                            <small>P.O.Box : 83481, Dubai, United Arab Emirates</small>
                        </div>
                        <div><small>+971 4 3939 562 / +971 55 330 3991</small></div>
                        <div><small>mail@akilgroup.com, www.akilgroup.com</small></div>
                        <div><small>TRN : 100391417100003</small></div>
                    </div>
                </td>
                <td>
                    <div class="company-details text-rights" style="float:right;clear: both;" >
                        Bill to <div class="sub-heading">{{ $vendor->title }} {{ $vendor->vendor_display_name }}</div>
                        <div><small>{{ $vendor->work_phone }} / {{ $vendor->mobile }}</small></div>
                        <div><small>{{ $vendor->email }}</small></div>
                        <div><small>{{ $vendor->address }}</small></div>
                        <div><small>TRN :{{ $vendor->tax_number }}</small></div>
                    </div>
                </td>
            </tr>
        </table>
        {{-- <table class="table">
            <tr>
                <td>
                    <div class="company-details">
                        Bill to <div class="sub-heading">{{ $vendor->title }} {{ $vendor->vendor_display_name }}</div>
                        <div><small>{{ $vendor->work_phone }} / {{ $vendor->mobile }}</small></div>
                        <div><small>{{ $vendor->email }}</small></div>
                        <div><small>{{ $vendor->address }}</small></div>
                        <div><small>TRN :{{ $vendor->tax_number }}</small></div>
                    </div>
                </td>
            </tr>
        </table> --}}
        <div class="voucher" style="margin-top:30px;">
            <table>
                <tr>
                    <td colspan="2">Amount: </td>
                    <td>Date: {{ date('d-M-y') }}</td>
                </tr>
                <tr>
                    <td colspan="2">Payment Mode: {{ $payment->payment_mode }}</td>
                    <td>Ref# {{ $payment->payment_mode_ref }}</td>
                </tr>
              
                <tr>
                    <td colspan="3">The Sum of: {{ $expense->total }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 40px">Paid For:

                            <br>
                            @foreach ($items as $item)
                                <ul>
                                    <li>{{($item->detail)}} <br> <small style="font-size: 9px"> Rate: {{$item->rate}} x Qty {{$item->qty}} = Amount {{$item->amount}}</small></li>
                                </ul>
                            @endforeach

                    </td>
                    <td style="padding-bottom: 40px">Payee: {{ $payment->payee }}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 40px">Approved By:</td>
                    <td style="padding-bottom: 40px">Paid By:</td>
                    <td style="padding-bottom: 40px">Signature:</td>
                </tr>
            </table>

            <div class="footer">
                <p>This is a system-generated payment voucher.</p>
            </div>
        </div>
    </div>

    <div class="container">
        <table class="table">
            <tr>
                <td>
                    <div>
                        <div class="image-box">
                            <img class="w-100" src="https://amc.mytime2cloud.com/mail-logo.png"
                                alt="Akil Security & Alarm Systems LLC" />
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <div>
                        <div class="sub-heading">Payment Voucher</div>
                        <div><small>Exp Ref # {{ $expense->id < 1000 ? '00' : '' }}{{ $expense->id }}</small></div>
                        <div><small>Date: <span>{{ date('d-M-y') }}</span></small></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="company-details">
                        <div class="sub-heading">Akil Security & Alarm Systems LLC</div>
                        <div>
                            <small>P.O.Box : 83481, Dubai, United Arab Emirates</small>
                        </div>
                        <div><small>+971 4 3939 562 / +971 55 330 3991</small></div>
                        <div><small>mail@akilgroup.com, www.akilgroup.com</small></div>
                        <div><small>TRN : 100391417100003</small></div>
                    </div>
                </td>
                <td>
                    <div class="company-details text-rights" style="float:right;clear: both;" >
                        Bill to <div class="sub-heading">{{ $vendor->title }} {{ $vendor->vendor_display_name }}</div>
                        <div><small>{{ $vendor->work_phone }} / {{ $vendor->mobile }}</small></div>
                        <div><small>{{ $vendor->email }}</small></div>
                        <div><small>{{ $vendor->address }}</small></div>
                        <div><small>TRN :{{ $vendor->tax_number }}</small></div>
                    </div>
                </td>
            </tr>
        </table>
        {{-- <table class="table">
            <tr>
                <td>
                    <div class="company-details">
                        Bill to <div class="sub-heading">{{ $vendor->title }} {{ $vendor->vendor_display_name }}</div>
                        <div><small>{{ $vendor->work_phone }} / {{ $vendor->mobile }}</small></div>
                        <div><small>{{ $vendor->email }}</small></div>
                        <div><small>{{ $vendor->address }}</small></div>
                        <div><small>TRN :{{ $vendor->tax_number }}</small></div>
                    </div>
                </td>
            </tr>
        </table> --}}
        <div class="voucher" style="margin-top:30px;">
            <table>
                <tr>
                    <td colspan="2">Amount: </td>
                    <td>Date: {{ date('d-M-y') }}</td>
                </tr>
                <tr>
                    <td colspan="2">Payment Mode: {{ $payment->payment_mode }}</td>
                    <td>Ref# {{ $payment->payment_mode_ref }}</td>
                </tr>
              
                <tr>
                    <td colspan="3">The Sum of: {{ $expense->total }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 40px">Paid For:

                            <br>
                            @foreach ($items as $item)
                                <ul>
                                    <li>{{($item->detail)}} <br> <small style="font-size: 9px"> Rate: {{$item->rate}} x Qty {{$item->qty}} = Amount {{$item->amount}}</small></li>
                                </ul>
                            @endforeach

                    </td>
                    <td style="padding-bottom: 40px">Payee: {{ $payment->payee }}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 40px">Approved By:</td>
                    <td style="padding-bottom: 40px">Paid By:</td>
                    <td style="padding-bottom: 40px">Signature:</td>
                </tr>
            </table>

            <div class="footer">
                <p>This is a system-generated payment voucher.</p>
            </div>
        </div>
    </div>
</body>

</html>
