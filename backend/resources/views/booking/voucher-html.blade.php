<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Voucher</title>
    <style>
        body {
            margin: 0 30px;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #636363;
            font-size: 13px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
        }

        .section {
            margin-top: 18px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 8px;
            padding-bottom: 4px;
            color: #444;
        }

        .label {
            font-weight: bold;
            color: #444;
            display: inline-block;
            width: 150px;
        }

        .row {
            margin-bottom: 6px;
            font-size: 12px;
        }

        table {
            width: 100%;
        }

        .paid {
            font-weight: bold;
            font-size: 13px;
        }

        .small {
            font-size: 11px;
        }

        .wrapper {
            border: 1px solid rgb(202, 200, 200);
            border-radius: 10px;
            padding: 20px;

        }
    </style>
</head>

<body>
    <div class="container">
        @php
            $logoPath = public_path('upload/' . $company->logo_name);
        @endphp


        <table width="100%" style="margin-bottom: 20px;">
            <tr>

                <td width="30%" align="left">
                    <img src="{{ $logoPath }}" alt="Logo" height="75">
                </td>
                <td width="40%" align="center">
                    <h3 style="margin: 0; font-size: 16px; color: #444;">Booking Voucher</h3>
                </td>
                <td width="30%" align="right" style="font-size: 11px;">
                    <p>Reservation # <strong>{{ $data['reservation_no'] }}</strong></p>
                    <p>Booked on: <strong>{{ $data['booked_date'] }}</strong></p>
                </td>
            </tr>
        </table>

        <div class="wrapper">
            <div class="section">
                <p style="margin-bottom:10px;">{{$company->location ?? "---"}}</p>
                <p>Phone: <span style="color: rgb(100, 100, 250)">{{$company?->contact?->number}}, {{$company?->contact?->whatsapp}}</span></p>
                <p>Email: <span style="color: rgb(100, 100, 250)">{{$company?->user?->email ?? "---"}}</span></p>
            </div>
            <div class="section">
                <div class="section-title"></div>
                <p class="row"><span class="label">Check-In:</span> {{$data['check_in']}}</p>
                <p class="row"><span class="label">Check-Out:</span> {{$data['check_out']}}</p>
                <p class="row"><span class="label">Nights:</span> {{$data["nights"]}} Nights</p>
            </div>

            <div class="section">
                <div class="section-title"></div>
                <p class="row"><span class="label">Guest Name:</span> {{$data["primary_guest"]}} (Primary Guest)</p>
                <p class="row"><span class="label">Email:</span> {{$data["email"]}}</p>
                <p class="row"><span class="label">Phone:</span> {{$data["phone"]}}</p>
                <p class="row"><span class="label">Guests:</span> {{$data["adults"]}} Adults</p>
            </div>

            <div class="section">
                <div class="section-title"></div>
                <p class="row"><span class="label">Room Type:</span> {{$data["room_type"]}}</p>
                {{-- <p class="row"><span class="label">Includes:</span> {{$data["room_no"]}}</p> --}}
                {{-- <p class="row small">• Complimentary Tea & Coffee during Hi-Tea (4:30 PM – 5:30 PM)</p>
                <p class="row small">• Evening Turndown Delight – Signature hot chocolate served during evening</p> --}}
            </div>

            <div class="section">
                <div class="section-title"></div>
                <p class="paid">Booking Price: INR {{$data["total_price"]}}</p>
            </div>



            <div style="text-align: center;">
                <img src="{{ public_path('logos/booking-confirmed.png') }}" alt="" height="175">
            </div>

            <div class="section">
                <div class="section-" style="color: red">
                    This booking is non-refundable. You will not get a refund if you cancel this booking.
                </div>
            </div>

        </div>
    </div>
</body>

</html>
