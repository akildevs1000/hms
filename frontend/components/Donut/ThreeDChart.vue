<template>
    <highcharts :options="chartOptions"></highcharts>
  </template>
  
  <script>
  export default {
    data() {
      return {
        chartOptions: {
          chart: {
            type: 'pie',
            options3d: {
              enabled: true,
              alpha: 45,
              beta: 0
            }
          },
          title: {
            text: '3D Donut Chart Example'
          },
          plotOptions: {
            pie: {
              innerSize: 100,
              depth: 45
            }
          },
          series: [{
            name: 'Data',
            data: [
              ['Apples', 8],
              ['Oranges', 3],
              ['Pears', 4],
              ['Bananas', 6],
              ['Plums', 2]
            ]
          }]
        }
      };
    }
  };
  </script>
  