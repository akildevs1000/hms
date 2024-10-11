<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .card {
            width: 300px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            padding: 20px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .chart-container {
            width: 150px;
            height: 150px;
            position: relative;
        }

        .chart-container .chart-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            font-weight: bold;
        }

        .info-container {
            flex-grow: 1;
            margin-left: 20px;
        }

        .info-container h4 {
            margin-top: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .info-table td {
            padding: 4px 8px;
        }

        .info-table tr:last-child td {
            color: red;
        }

        .info-table .right-align {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="chart-container">
        <!-- Highcharts donut chart will be rendered here -->
        <div id="donutChart" style="width: 150px; height: 150px;"></div>
        <div class="chart-label">15</div>
        <img src="{{ getcwd() . '/upload/room-chart.png' }}" alt="Room Chart" />

    </div>
    <div class="info-container">
        <h4>Room</h4>
        <table class="info-table">
            <tr>
                <td>Check In</td>
                <td class="right-align">10</td>
            </tr>
            <tr>
                <td>Continue</td>
                <td class="right-align">2</td>
            </tr>
            <tr>
                <td>Day use</td>
                <td class="right-align">3</td>
            </tr>
            <tr>
                <td>Comp Room</td>
                <td class="right-align">0</td>
            </tr>
            <tr>
                <td>Check Out</td>
                <td class="right-align">3</td>
            </tr>
            <tr>
                <td>Closing</td>
                <td class="right-align">8</td>
            </tr>
        </table>
    </div>
</div>

<!-- Include Highcharts library -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    // Initialize the Highcharts donut chart
    Highcharts.chart('donutChart', {
        chart: {
            type: 'pie',
            height: 150,
            width: 150,
        },
        title: {
            text: null
        },
        tooltip: {
            enabled: false
        },
        plotOptions: {
            pie: {
                innerSize: '70%', // Creates the donut effect
                dataLabels: {
                    enabled: false
                }
            }
        },
        series: [{
            data: [
                { name: 'Check In', y: 10, color: '#28a745' },
                { name: 'Continue', y: 2, color: '#ffc107' },
                { name: 'Day Use', y: 3, color: '#17a2b8' },
                { name: 'Comp Room', y: 0, color: '#6c757d' },
                { name: 'Check Out', y: 3, color: '#007bff' },
                { name: 'Closing', y: 8, color: '#dc3545' }
            ]
        }],
        credits: {
            enabled: false // Remove Highcharts branding
        }
    });
</script>

</body>
</html>
