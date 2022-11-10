<!DOCTYPE html>
<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .pie-chart {
            width: 900px;
            height: 500px;
            margin: 0 auto;
        }
    </style>
    {{-- make sure you are using http, and not https --}}
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>

    <script type="text/javascript">
        function init() {
            google.load("visualization", "1.1", {
                packages: ["corechart"],
                callback: 'drawCharts'
            });
        }

        function drawCharts() {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Coding', 11],
                ['Eat', 1],
                ['Commute', 2],
                ['Looking for code Problems', 4],
                ['Sleep', 6]
            ]);
            var options = {
                title: 'My Daily Activities',
            };
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>

<body onload="init()">
    <div id="piechart" class="pie-chart"></div>
</body>

</html>
