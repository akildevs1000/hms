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

        tr:nth-child(even) {
            /* background-color: #eeeeee; */
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
    </style>
</head>

<body>
    <table style="margin-top: -20px !important;backgroundd-color:blue;padding-bottom:0px ">
        <tr>
            <td style="text-align: left;width: 300px; border :none; padding:15px;   backgrozund-color: red">
                <div style=";">
                    <img src="{{ getcwd() . '/upload/app-logo.jpeg' }}" height="70px" width="200">
                </div>
            </td>
            <td style="text-align: left;width: 333px; border :none; padding:15px; backgrozusnd-color:blue">
                <div>
                    <table style="text-align: left; border :none;  ">
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: center; border :none">
                                <span class="title-font">
                                    Weekly Attendance Summary Report
                                </span>
                                <hr style="width: 230px">
                            </td>
                        </tr>
                        <tr style="text-align: left; border :none;">
                            <td style="text-align: center; border :none">
                                <span style="font-size: 11px">
                                    01 Oct 2022 - 30 Oct 2022
                                </span>
                                <hr style="width: 230px">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="text-align: right;width: 300px; border :none; backgrounsd-color: red">


                <table class="summary-table"
                    style="border:none; padding:0px 50px; margin-left:35px;margin-top:20px;margin-bottom:0px">
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <b>
                                Akkil Security & Alarm System LLC
                            </b>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 3px"> P.O. Box 83481, Dubai </span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 3px"> United Arab Emirates </span>
                            <br>
                        </td>
                    </tr>
                    <tr style="text-align: left; border :none;">
                        <td style="text-align: right; border :none;font-size:10px">
                            <span style="margin-right: 3px"> Tel: +97143939562 </span>
                            <br>
                        </td>
                    </tr>
                </table>

                <br>
            </td>
            </td>
        </tr>
    </table>
    <hr style="margin:0px;padding:0">
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Fahath</td>
            <td style="text-align: left;"><b>EID</b>: 000222</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>
    <table style="margin-top: 5px !important;">
        <tr style="background-color: #A6A6A6;">
            <td><b>Dates</b></td>
            @for ($i = 1; $i <= 7; $i++)
                <td style="text-align: center;"> {{ $i }} </td>
            @endfor
        </tr>

        <tr style="background-color: none;">
            <td> <b>Days</b> </td>
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            @endphp
            @foreach ($days as $item)
                <td style="text-align: center;"> {{ $item }} </td>
            @endforeach
        </tr>
        <tr>
            <td> <b>Status</b> </td>

            @for ($i = 1; $i <= 7; $i++)
                <?php
                $my_array = ['A', 'P'];
                shuffle($my_array);
                ?>
                <td style=" text-align: center;"><span
                        style="color:{{ $my_array[0] == 'A' ? 'red' : 'green' }};">{{ $my_array[0] }}</span></td>
            @endfor
        </tr>

    </table>
    <table style="margin-top: 5px !important;">
        <tr style="text-align: left; border :1px solid black; width:120px;">
            <td style="text-align: left;"><b>Name</b>: Arvind</td>
            <td style="text-align: left;"><b>EID</b>: 000333</td>
            <td style="text-align: left;"><b>Dept</b>: Sales</td>
            <td style="text-align: left; width:120px;"><b>Date: </b> 1 Sep 22 to 13 Sep 22</td>
            <td style="text-align: left;"><b>Total Hrs</b>: 150</td>
            <td style="text-align: left;"><b>OT</b>: 10:31</td>
            <td style="text-align: left; color: green;"><b>Present</b>: 14</td>
            <td style="text-align: left; color: red;"><b>Absent</b>: 14</td>
            <td style="text-align: left; color: rgb(209, 139, 9);"><b>Late</b>: 14</td>


        </tr>
    </table>


    <hr style=" bottom: 0px; position: absolute; width: 100%; margin-bottom:40px">
    <footer style="padding-top: 100px!important">
        <table class="main-table">
            <tr style="border :none">
                <td style="text-align: left;border :none"><b>Device</b>: Main Entrance = MED, Back Entrance = BED</td>
                <td style="text-align: left;border :none"><b>Shift Type</b>: Manual = MA, Auto = AU, NO = NO</td>
                <td style="text-align: left;border :none"><b>Shift</b>: Morning = Mor, Evening = Eve, Evening2 = Eve2
                </td>
                <td style="text-align: right;border :none;">
                    <b>Powered by</b>: <span style="color:blue"> www.ideahrms.com</span>
                </td>
                <td style="text-align: right;border :none">
                    Printed on : {{ date('d-M-Y ') }}
                </td>
            </tr>
        </table>
    </footer>


</body>

</html>
