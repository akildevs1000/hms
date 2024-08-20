<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Simple Form</title>
    <style>
        * {
            box-sizing: border-box;
        }

        .m-1 {
            margin: 0.25rem;
        }

        .m-2 {
            margin: 0.5rem;
        }

        .m-3 {
            margin: 1rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mr-1 {
            margin-right: 0.25rem;
        }

        .ml-3 {
            margin-left: 1rem;
        }

        .mx-4 {
            margin-right: 1.5rem;
            margin-left: 1.5rem;
        }

        .my-5 {
            margin-top: 2.5rem;
            margin-bottom: 2.5rem;
        }

        .pr-1 {
            padding-right: 0.25rem;
        }

        .pt-2 {
            padding-top: 0.5rem;
        }

        .pl-3 {
            padding-left: 1rem;
        }

        .px-4 {
            padding-right: 1.5rem;
            padding-left: 1.5rem;
        }

        .py-5 {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }

        .row::after {
            content: "";
            clear: both;
            display: table;
        }

        .col {
            width: 5%;
            float: left;
            padding: 5px;
        }


        .col-1 {
            width: 8.33%;
            float: left;
            padding: 5px;
        }

        .col-2 {
            width: 16.66%;
            float: left;
            padding: 5px;
        }

        .col-3 {
            width: 24.99%;
            float: left;
            padding: 5px;
        }

        .col-4 {
            width: 33.32%;
            float: left;
            padding: 5px;
        }

        .col-5 {
            width: 41.65%;
            float: left;
            padding: 5px;
        }

        .col-6 {
            width: 49.98%;
            float: left;
            padding: 5px;
        }

        .col-7 {
            width: 58.31%;
            float: left;
            padding: 5px;
        }

        .col-8 {
            width: 66.64%;
            float: left;
            padding: 5px;
        }

        .col-9 {
            width: 74.97%;
            float: left;
            padding: 5px;
        }

        .col-10 {
            width: 83.3%;
            float: left;
            padding: 5px;
        }

        .col-11 {
            width: 91.63%;
            float: left;
            padding: 5px;
        }

        .col-12 {
            width: 100%;
            float: left;
            padding: 5px;
        }

        .form-input {
            width: 100%;
            padding: 2px 5px;
            border-radius: 0px;
            resize: vertical;
            outline: 0;
        }

        .label-txt {
            font-size: 14px
        }

        ,
        input {
            /* border: none; */
            /* border-bottom: 1px solid black; */
            padding: 5px 10px;
            outline: none;
            font-size: 13px
        }

        hr {
            position: relative;
            border: none;
            height: 1px;
            background: rgb(167, 164, 164);
        }

        .terms {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif
        }

        .header-txt {
            font-size: 20px;
            font-weight: bolder;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header-txt-span {
            font-size: 12px;
            font-weight: bolder;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .header-txt-address {
            font-size: 12px;
            font-weight: bolder;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px;
            padding: 0px;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #e9e9e9;
            width: 50%;
            margin: auto;
        }

        td,
        th {
            font-size: 12px;
            text-align: left;
            padding: 2px 2px;
            border: 1px solid #e9e9e9;
        }
    </style>
</head>

<body>

    <div class="row">
        <div class="col-4">
            <p class="header-txt">HYDERS PARK</p>
            <span
                class="header-txt-span">{{ $booking->company_id == 1 ? 'The Business Hotel' : 'The Luxuery Hotel' }}</span>
        </div>
        <div class="col-4" style="margin: 0px">
            {{-- @if ($booking->company_id == 1)
                <img src="{{ getcwd() . '/upload/app-logo.jpg' }}" height="70px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @elseif ($booking->company_id == 2)
                <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="100px" width="100"
                    style="margin-left: 50px;margin-top: 0px">
            @endif --}}

            <img src="{{ getcwd() . '/upload/app-logo.jpg' }}" height="70px" width="100"
                style="margin-left: 50px;margin-top: 0px">
        </div>
        <div class="col-4 header-txt-address" style="text-align:right">
            <div style="text-align:left; margin-left:70px" class="header-txt-address">
                <small> GST No: {{ $booking->company->mol_id ?? '33CKQPM1598B2ZZ' }} </small><br>
                {{-- 04542-291888 --}}
                <small> Telephone No: {{ $booking->company->contact->number ?? '04542291888' }} </small><br>
                <small> Email: {{ $booking->company->user->email ?? '---' }}</small>
                <small> Guest Registration Date : {{ date('Y/m/d') }}</small>
            </div>
        </div>
    </div>

    <hr>


    <div class="row mt-3">
        <div class="col-2">
            <label for="name" class="label-txt">Customer Picture:</label>
        </div>
        <div class="col-10">

            @if (env('APP_ENV') == 'local')
                <img src="https://backend.myhotel2cloud.com/sign/sign-1724149220.png" alt="">
            @else
                <img src="{{ $booking->customer->captured_photo ?? '' }}" alt="">
            @endif

        </div>
    </div>
    <hr>

    <div class="row mt-3">
        <div class="col-2">
            <label for="name" class="label-txt">Customer ID (Front):</label>
        </div>
        <div class="col-10">

            @if (env('APP_ENV') == 'local')
                <img src="https://backend.myhotel2cloud.com/sign/sign-1724149220.png" alt="">
            @else
                <img src="{{ $booking->customer->id_frontend_side ?? '' }}" alt="">
            @endif


        </div>
    </div>
    <hr>

    <div class="row mt-3">
        <div class="col-2">
            <label for="name" class="label-txt">Customer ID (Back):</label>
        </div>
        <div class="col-10">

            @if (env('APP_ENV') == 'local')
                <img src="https://backend.myhotel2cloud.com/sign/sign-1724149220.png" alt="">
            @else
                <img src="{{ $booking->customer->id_backend_side ?? '' }}" alt="">
            @endif


        </div>
    </div>




    @php
        function numFormat($n = null)
        {
            if (!$n) {
                return '---';
            }
            return number_format($n, 2) ?? '---';
        }
    @endphp
</body>

</html>
