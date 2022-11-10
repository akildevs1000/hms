<template>
  <div>
    <v-row>
      <div id="areaChart" class="p-2"></div>
    </v-row>
  </div>
</template>

<script>
export default {
 

  data() {
    return {
      options: {
        series: [
          {
            name: "Desktops",
            data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
          },
          {
            name: "Desktops",
            data: [7, 56, 68, 6, 23, 4, 34, 123, 12]
          }
        ],
        chart: {
          height: 350,
          type: "area",
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: "straight"
        },
        title: {
          text: "DAILY ATTENDANCE REPORT",
          align: "left"
        },
        grid: {
          row: {
            colors: ["#f3f3f3", "transparent"], // takes an array which will be repeated on columns
            opacity: 0.5
          }
        },
        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep"
          ]
        }
      }
    };
  },
  mounted() {
    var chart = new ApexCharts(
      document.querySelector("#areaChart"),
      this.options
    );
    chart.render();
  }
};
</script>
