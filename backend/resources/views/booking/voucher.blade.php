<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Voucher</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .container { width: 100%; padding: 20px; }
        .header, .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; font-size: 18px; border-bottom: 1px solid #ccc; margin-bottom: 10px; }
        .row { margin-bottom: 10px; }
        .label { font-weight: bold; display: inline-block; width: 150px; }
    </style>
</head>
<body>
<div class="container">
   <table width="100%" style="margin-bottom: 20px;">
    <tr>
        {{-- Logo on the left --}}
        <td width="30%" align="left">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo" height="50">
        </td>

        {{-- Title in the center --}}
        <td width="40%" align="center">
            <h2 style="margin: 0;">Booking Voucher</h2>
        </td>

        {{-- Booking Info on the right --}}
        <td width="30%" align="right" style="font-size: 12px;">
            <p style="margin: 0;"><strong>Booking ID:</strong> {{ $booking_id }}</p>
            <p style="margin: 0;"><strong>PNR:</strong> {{ $pnr }}</p>
            <p style="margin: 0;"><strong>Booked on:</strong> {{ $booked_date }}</p>
        </td>
    </tr>
</table>


    <div class="section">
        <h3 class="section-title">Hotel Details</h3>
        <p><strong>{{ $hotel_name }}</strong></p>
        <p>{{ $hotel_address }}</p>
        <p>📞 {{ $hotel_phone }}</p>
        <p>📧 {{ $hotel_email }}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Stay Details</h3>
        <p><span class="label">Check-in:</span> {{ $checkin_date }}</p>
        <p><span class="label">Check-out:</span> {{ $checkout_date }}</p>
        <p><span class="label">Guests:</span> {{ $guests }} ({{ $guest_type }})</p>
        <p><span class="label">Primary Guest:</span> {{ $primary_guest }}</p>
        <p><span class="label">Email:</span> {{ $email }}</p>
        <p><span class="label">Phone:</span> {{ $phone }}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Room Details</h3>
        <p><span class="label">Room Type:</span> {{ $room_type }}</p>
        <p><span class="label">Bed:</span> {{ $bed }}</p>
        <p><span class="label">Adults:</span> {{ $adults }}</p>
        <p><span class="label">Breakfast:</span> Included</p>
        <p>✔ Complimentary Tea & Coffee (4:30–5:30 PM)</p>
        <p>✔ Evening hot chocolate delight</p>
    </div>

    <div class="section">
        <h3 class="section-title">Payment Details</h3>
        <p><span class="label">Paid Amount:</span> ₹{{ $amount }} (via {{ $payment_method }})</p>
        <p style="color: red;"><strong>This booking is non-refundable.</strong></p>
    </div>
</div>
</body>
</html>
