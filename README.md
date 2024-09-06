# hms

booked status = 1,
booked check in status = 2
booked check out status = 3
booked maintanance status = 4
booked available status = 0






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }

        .header,
        .footer {
            text-align: center;
        }

        .quotation-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .company-info {
            text-align: right;
        }

        .guest-info,
        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .guest-info td,
        .table th,
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .table th {
    background-color: #007BFF; /* Blue background */
    color: white; /* White text */
    padding: 8px;
    border: 1px solid #ddd;
}


        .table .right-align {
            text-align: right;
        }

        .notes,
        .terms {
            margin-bottom: 20px;
        }

        .acknowledgement {
            margin-top: 40px;
            font-weight: bold;
        }

        .signature {
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            width: 40%;
            text-align: center;
        }

        .logo {
            text-align: left;
        }

        .discount {
            color: red;
        }
    </style>
</head>

<body>

   

   

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>01</td>
                <td>Lucky Hall (Minimum 4 HRS)</td>
                <td>1</td>
                <td class="right-align">15,000.00</td>
                <td class="right-align">15,000.00</td>
            </tr>
            <tr>
                <td>02</td>
                <td>Veg Meals (Menu A)</td>
                <td>100 Pax</td>
                <td class="right-align">100.00</td>
                <td class="right-align">10,000.00</td>
            </tr>
            <tr>
                <td>03</td>
                <td>Evening Snacks</td>
                <td>100 Pax</td>
                <td class="right-align">25.00</td>
                <td class="right-align">750.00</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right-align">Sub Total</td>
                <td class="right-align">25,750.00</td>
            </tr>
            <tr>
                <td colspan="4" class="right-align discount">Discount</td>
                <td class="right-align">-1,000.00</td>
            </tr>
            <tr>
                <td colspan="4" class="right-align"><strong>Total Rs.</strong></td>
                <td class="right-align"><strong>24,250.00</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="notes">
        <p><strong>Additional Charges:</strong></p>
        <p>Extra hour charge: Rs. 4000 per hr</p>
        <p>AC hall: 15000 | Non-AC hall: 12000</p>
        <p>Damage Deposit: Rs. 10,000</p>
        <p>Cleaning Charges: 1500</p>
    </div>

    <div class="terms">
        <p><strong>Terms and Conditions:</strong></p>
        <p>Advance Payment: 25% of advance payment is required to confirm the hall booking.</p>
        <p>Balance Payment: Full payment should be completed 2 days prior to the event date.</p>
        <p>GST: 18% of the total value will be charged on services if the amount exceeds Rs. 20,000.</p>
    </div>

    <div class="acknowledgement">
        I hereby acknowledge and agree to all the terms and conditions outlined above.
    </div>

    <div class="signature">
        <div>
            Guest Signature
        </div>
        <div>
            Manager
        </div>
    </div>

</body>

</html>

 
