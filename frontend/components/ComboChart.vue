<template>
  <div>
    <v-row>
      <div id="comboChart" class="p-2"></div>
    </v-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      days: [],
      options: {
        // colors: ["#A24FDD", "#6DFCCA", "#E78956", "#3A95D9"],
        series: [
          {
            name: "Today Summary",
            type: "column",
            data: []
            // background: "black"
          },
          {
            name: "Today Presents",
            type: "column",
            data: []
          },
          {
            name: "Today Absent",
            type: "column",
            data: []
          },
          {
            name: "Today Missing",
            type: "column",
            data: []
          }
        ],
        chart: {
          height: 350,
          // width: 0,
          type: "line",
          stacked: false
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: [1, 1, 1]
        },
        title: {
          text: "MONTHLY ATTENDANCE REPORT",
          align: "left",
          offsetX: 110
        },
        xaxis: {
          categories: []
        },
        // yaxis: [
        //   {
        //     axisTicks: {
        //       show: true
        //     },
        //     axisBorder: {
        //       show: true,
        //       color: "#008FFB"
        //     },
        //     labels: {
        //       style: {
        //         colors: "#008FFB"
        //       }
        //     },

        //     tooltip: {
        //       enabled: true
        //     }
        //   }
        // ],
        tooltip: {
          fixed: {
            enabled: true,
            position: "topLeft", // topRight, topLeft, bottomRight, bottomLeft
            offsetY: 30,
            offsetX: 60
          }
        },
        legend: {
          horizontalAlign: "left",
          offsetX: 40
        }
      }
    };
  },
  mounted() {
    this.getDaysInCurrentMonth;

    var chart = new ApexCharts(
      document.querySelector("#comboChart"),
      this.options
    );
    chart.render();
  },
  computed: {
    getDaysInCurrentMonth() {
      const date = new Date();
      let lastDay = new Date(
        date.getFullYear(),
        date.getMonth() + 1,
        0
      ).getDate();
      for (let i = 1; i <= lastDay; i++) {
        // this.options.xaxis.categories.push(i);
      }
      for (let i = 1; i <= lastDay; i++) {
        // let r = Math.floor(Math.random() * 20);
        this.options.series[0].data.push(Math.floor(Math.random() * 20));
        this.options.series[1].data.push(Math.floor(Math.random() * 20));
        this.options.series[2].data.push(Math.floor(Math.random() * 20));
        this.options.series[3].data.push(Math.floor(Math.random() * 20));
      }
    }
  },
  methods: {}
};
</script>
