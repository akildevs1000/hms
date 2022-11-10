<template>
  <div style="padding: 0px ;width:100%">
    <div id="pie"></div>
  </div>
</template>

<script>
export default {
  props: ["items"],
  data() {
    return {
      options: {
        title: {
          text: "DAILY ATTENDANCE REPORT",
          align: "left",
          margin: 50
        },
        colors: ["#A24FDD", "#6DFCCA", "#E78956", "#3A95D9"],

        series: [],
        chart: {
          type: "pie",
          width: 590,
          height: 450
        },
        labels: [],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        responsive: [
          {
            breakpoint: 1400,
            options: {
              chart: {
                width: 350,
                margin: 50,
                height: 4500
              },
              legend: {
                position: "bottom"
              }
            }
          }
        ]
      }
    };
  },
  mounted() {
    this.options.labels = this.items.map(e => e.title);
    this.options.series = this.items.map(e => e.value);
    new ApexCharts(document.querySelector("#pie"), this.options).render();
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
