<template>
  <div id="donut"></div>
</template>

<script>
export default {
  props: ["items"],

  data() {
    return {
      options: {
        title: {
          text: "DAILY ATTENDANCE REPORT",
          align: "left"
        },
        colors: ["#A24FDD", "#6DFCCA", "#E78956", "#3A95D9"],

        series: [],
        chart: {
          type: "donut"
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
            breakpoint: 480,
            options: {
              chart: {
                width: 200
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
  created() {
    this.getTitle();
    this.getValue();
  },
  mounted() {
    var chart = new ApexCharts(document.querySelector("#donut"), this.options);
    chart.render();
  },
  methods: {
    getTitle() {
      this.options.labels = this.items.map(e => e.title);
    },
    getValue() {
      this.options.series = this.items.map(e => e.value);
      console.log(this.options.series);
    }
  }
};
</script>
