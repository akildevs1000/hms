<template>
  <div style="padding: 0px ;width:90%;">
    <div id="pie" style="height: 100px"></div>
  </div>
</template>

<script>
export default {
  props: ["renderChartData"],
  data() {
    return {
      options: {
        series: [
          {
            data: []
          }
        ],
        chart: {
          type: "bar",
          height: 150,
          toolbar: {
            show: false
          }
        },

        colors: ["#5768D5", "#4290CB", "#EB895A", "#51AF95"],

        plotOptions: {
          bar: {
            horizontal: true,
            distributed: true,
            dataLabels: {
              position: "top"
            }
          }
        },
        dataLabels: {
          enabled: true,
          offsetX: -6,
          style: {
            fontSize: "12px",
            colors: ["#fff"]
          }
        },
        stroke: {
          show: true,
          width: 1,
          colors: ["#fff"]
        },
        tooltip: {
          shared: true,
          intersect: false
        },
        xaxis: {
          categories: [
            "Expect Check In",
            "Expect Check Out",
            "Booked",
            "Available"
          ],
          labels: {
            show: false
          }
        },
        yaxis: {
          labels: {
            show: false
          }
        }
      }
    };
  },

  mounted() {
    setTimeout(
      () => {
        this.options.series[0].data = this.renderChartData.map(e => e);
        console.log(this.options.series[0].data);
        new ApexCharts(document.querySelector("#pie"), this.options).render();
      },

      1000
    );
  },
  methods: {}
};
</script>

<style>
/* .apexcharts-legend-series {
  margin: 0px 100px 2px 0px !important;
} */
#pie .apexcharts-legend-series {
  margin: 0px 50px 2px 0px !important;
}
</style>
