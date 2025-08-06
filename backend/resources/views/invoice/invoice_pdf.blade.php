<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            font-weight: normal;
            color: #666666;
        }

        .wrapper {
            min-height: 90%;
            position: relative;
        }

        .container {
            width: 90%;
            margin: 35px auto;
            padding: 15px;
            padding-bottom: 80px;
            /* Footer space */
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-table .right {
            text-align: right;
        }

        .title {
            text-align: center;
            font-size: 18px;
        }

        .info-box {
            border-radius: 15px;
            background: #f5f6fa;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .pricing-box {
            border-radius: 7px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .info-table {
            padding: 10px;
        }

        .info-table td {
            padding: 6px;
            font-size: 12px;
        }

        .pricing-table td {
            padding: 4px 10px;
            text-align: center;
            font-size: 12px;
            border-bottom: 1px solid #ddd;
        }

        .pricing-table tr:last-child td {
            border-bottom: none;
        }

        .total-section {
            margin-top: 15px;
            font-size: 13px;
            text-align: right;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            font-size: 12px;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .line {
            border: none;
            height: 1px;
            background-color: #dbdfea;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <table class="header-table" style="margin-bottom: 30px">
                <tr>
                    <td>
                        @if (env('APP_ENV') == 'production')
                            <img src="{{ urldecode($company->logo) }}" height="100px" width="100"
                                style="margin-left: 50px;margin-top: 0px">
                        @else
                            <img src="https://backend.ezhms.com/upload/app-logo.jpeg" alt="Logo"
                                style="max-height:100px!important">
                        @endif
                        <br>
                    </td>
                    <td class="right">
                        <strong>{{ $company->name ?? '' }}</strong><br>
                        {{ $company->location[0] ?? '' }}<br>
                        {{ $company->location[1] ?? '' }}<br>
                        {{ $company?->user?->email ?? '' }}<br>
                        Phone: {{ $company?->contact?->number ?? '' }}<br>
                        {{ $company->mol_id }}
                    </td>
                </tr>
            </table>
            <hr class="line">
            <table class="invoice-table" style="padding: 5px 0px 15px 0px">
                <tr>
                    <td width="33%"></td>
                    <td width="33%">
                        <div class="title">Invoice</div>
                    </td>
                    <td width="33%" style="text-align: right; font-size: 13px;">
                        {{ $invoice }}
                    </td>
                </tr>
            </table>

            <div class="info-box">
                <table class="info-table">
                    <tr>
                        <td colspan="4"><strong>Guest Info:</strong></td>
                    </tr>
                    <tr>
                        <td width="25%">

                            @if ($customer->source)
                                {{ $booking->source ?? '' }}
                                <br>
                                GST: {{ $customer?->source?->gst ?? '---' }}
                            @elseif ($customer?->gst_number)
                                GST: {{ $customer?->gst_number ?? '---' }}
                                <br>
                            @endif

                            <div>
                                {{ ucfirst(strtolower($customer->full_name ?? '')) }}
                                <br>
                                {{ $customer->contact_no ?? '' }}
                                <br>
                                @if ($customer->city)
                                    <br>
                                    {{ $customer->city ?? '' }}
                                @endif
                                @if ($customer->state)
                                    <br>
                                    {{ $customer->state ?? '' }}
                                @endif
                                @if ($customer->zip_code)
                                    , {{ $customer->zip_code ?? '' }}
                                @endif
                                @if ($customer->country)
                                    <br>
                                    {{ $customer->country ?? '' }}
                                @endif
                            </div>
                        </td>
                        <td width="25%">
                            Check In:<br>
                            <b> {{ $booking['first_check_in_date'] }} <br>
                                {{ $booking['first_check_in_time'] }}</b><br>
                            ---<br><br>
                            Nights:<br><strong>1</strong>
                        </td>
                        <td width="25%">
                            Check Out:<br>
                            <b> {{ $booking['first_check_out_date'] }} <br>
                                {{ $booking['first_check_out_time'] }}</b><br>
                            ---<br><br>
                            Rooms:<br><strong>{{ $booking['total_rooms'] }}</strong>
                        </td>
                        <td width="25%">
                            Reservation No:<br>
                            <b> 439 <br>
                                02 Aug 2025</b><br>
                            <br><br>
                            Room Type:<br><strong>{{ $booking['room_types'] }}</strong>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="pricing-box" style="margin-top: 30px;">
                <table class="pricing-table">
                    <tbody>
                        <tr>
                            <td>Date</td>
                            <td>Room No</td>
                            <td>Unit</td>
                            <td style="text-align: right">Price</td>
                            <td style="text-align: right">SGST</td>
                            <td style="text-align: right">CGST</td>
                            <td style="text-align: right">Total</td>
                        </tr>
                        @php
                            $subtotal_price = 0;
                            $subtotal_cgst = 0;
                            $subtotal_sgst = 0;
                            $subtotal_total = 0;
                        @endphp
                        @foreach ($orderRooms as $room)
                            @php
                                // Room base calculations
                                $room_base = $room->inv_room_listing_price;
                                $room_sgst = $room->inv_room_sgst;
                                $room_cgst = $room->inv_room_cgst;
                                $room_misc_wo_tax = $room->miscellaneous_total_without_tax;
                                $room_misc_tax = $room->miscellaneous_tax;

                                // Subtotals for room
                                $subtotal_price += $room_base;
                                $subtotal_sgst += $room_sgst;
                                $subtotal_cgst += $room_cgst;
                                $subtotal_total += $room_base + $room_sgst + $room_cgst;

                                // Subtotals for miscellaneous if present
                                if ($room->miscellaneous_total > 0) {
                                    $subtotal_price += $room_misc_wo_tax;
                                    $subtotal_sgst += $room_misc_tax / 2;
                                    $subtotal_cgst += $room_misc_tax / 2;
                                    $subtotal_total += $room_misc_wo_tax + $room_misc_tax;
                                }
                            @endphp

                            <tr>
                                <td>{{ date('d M Y', strtotime($room->date)) }}</td>
                                <td>{{ $room->room_no }} ({{ $room->room_type }})</td>
                                <td>{{ $room->no_of_adult + $room->no_of_child }} (pax)</td>

                                <td style="text-align: right">
                                    {{ number_format($room_base + $room_misc_wo_tax, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ number_format($room_sgst, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ number_format($room_cgst, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ number_format($room_base + $room_misc_wo_tax + $room_sgst + $room_cgst, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="text-align: right" colspan="3"></td>
                            <td style="text-align: right"><b>{{ number_format($subtotal_price, 2) }}</b></td>
                            <td style="text-align: right"><b>{{ number_format($subtotal_sgst, 2) }}</b></td>
                            <td style="text-align: right"><b>{{ number_format($subtotal_sgst, 2) }}</b></td>
                            <td style="text-align: right"><b> {{ number_format($subtotal_total, 2) }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <table class="info-table">
                <tr>
                    <!-- Left side: Tax info -->
                    <td style="text-align: left; font-size: 12px; width: 60%;">
                        Tax Collected: {{ $currency }}
                        {{ number_format((float) ($subtotal_sgst + $subtotal_cgst), 2) }}<br>
                        SGST: {{ $currency }} {{ number_format((float) $subtotal_sgst, 2) }}<br>
                        CGST: {{ $currency }} {{ number_format((float) $subtotal_cgst, 2) }}<br><br>
                    </td>

                    <!-- Right side: Summary -->
                    <td style="font-size: 12px; width: 40%; text-align: right; padding: 0;">
                        <table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                            <tr>
                                <td style="padding: 6px; text-align: left;"><strong>Total:</strong></td>
                                <td style="padding: 6px; text-align: right;"><strong>{{ $currency }}
                                        {{ number_format($subtotal_total, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding: 6px; text-align: left;"><strong>Paid:</strong></td>
                                <td style="padding: 6px; text-align: right;">{{ $currency }}
                                    {{ number_format($booking['paid_amounts'], 2) }}</td>
                            </tr>
                            <tr style=" background: #f5f6fa;">
                                <td style="padding: 6px; text-align: left;"><strong>Balance:</strong></td>
                                <td style="padding: 6px; text-align: right;"><strong>{{ $currency }}
                                        {{ number_format($booking['balance'], 2) }}</strong></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div style="text-align: right; font-size: 12px; margin: 10px;">
                <br>Amount: {{$booking['amtLetter']}}
            </div>
        </div>
        <div class="footer">
            <strong>Thank you for choosing us. We look forward to welcoming you back soon.</strong><br>
            <div class="" style="padding: 0 100px">
                <hr class="line">
            </div>
            This is a system generated invoice and does not require signature.
        </div>
    </div>
</body>

</html>
