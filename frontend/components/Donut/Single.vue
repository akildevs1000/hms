<template>
    <div :style="`width:${size};`">
      <div :id="compId" class="chart"></div>
    </div>
  </template>
  
  <script>
  
  export default {
    props: ["compId", "labels", "series", "colors", "size"],
    data() {
      return {
        options: {
          dataLabels: {
            enabled: false, // Disable data labels
          },
          title: {
            align: "right",
          },
          colors: [],
  
          series: [],
          chart: {
            type: "donut",
            width: this.size,
            height: "1000px",
          },
          labels: [],
          plotOptions: {
            pie: {
              donut: {
                labels: {
                  show: true,
                  total: {
                    show: true,
                    label:"",
                    fontSize: "10px",
                    fontFamily: "Helvetica, Arial, sans-serif",
                  },
                  value: {
                    show: true,
                    fontSize: '14px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    color: '#373d3f',
                    offsetY: "-10px",
                    formatter: function (val) {
                      return val;
                    }
                  },
                },
              },
            },
          },
          responsive: [
            {
              breakpoint: 480,
              options: {
                chart: {
                  width: 200,
                },
                legend: {
                  position: "bottom",
                },
              },
            },
          ],
        },
      };
    },
    mounted() {
      this.options.labels = this.labels;
      this.options.series = this.series;
      this.options.colors = this.colors;
      var chart = new ApexCharts(
        document.querySelector("#" + this.compId),
        this.options
      );
      chart.render();
    },
  };
  </script>
  