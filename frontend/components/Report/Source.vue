<template>
  <v-row no-gutters>
    <v-col cols="7">
      <table>
        <thead>
          <tr>
            <td class="text-center">
              <small>COLOR</small>
            </td>
            <td><small>SOURCE</small></td>
            <td class="text-center"><small>NO OF ROOM</small></td>
            <td class="text-center"><small>PERCENTAGE %</small></td>
            <td class="text-center"><small>REVENUE</small></td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in data" :key="index">
            <td class="text-center">
              <v-icon :style="{ color: item.color, fontSize: '24px' }"
                >mdi-circle</v-icon
              >
            </td>
            <td>{{ item.source }}</td>
            <td class="text-center">{{ item.no_of_room }}</td>
            <td class="text-center">{{ item.percentage }}</td>
            <td class="text-center">{{ item.revenue }}</td>
          </tr>
          <tr>
            <td colspan="2">TOTAL</td>
            <td class="text-center">{{ totalRooms }}</td>
            <td class="text-center">100%</td>
            <td class="text-center">{{ totalRevenue }}</td>
          </tr>
        </tbody>
      </table>
    </v-col>
    <v-col cols="5" class="text-center">
      <v-tabs v-if="!loading" right>
        <v-tab>pie chart </v-tab>
        <v-tab>statistical </v-tab>
        <v-tab-item>
          <highcharts :options="pieChartOptions"></highcharts>
        </v-tab-item>
        <v-tab-item>
          <highcharts :options="barChartOptions"></highcharts>
        </v-tab-item>
      </v-tabs>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    heading: {
      default: "Revenue Flow Chat ( Source )",
    },
    data: {
      default: () => [],
    },
    colors: {
      default: () => [],
    },
  },

  data() {
    return {
      loading: true,
      pieChartOptions: {
        chart: {
          type: "pie",
          //   height: "320",
          options3d: {
            enabled: true,
            alpha: 45,
            beta: 0,
          },
        },
        colors: [],

        title: {
          text: "",
        },
        plotOptions: {
          pie: {
            depth: 45,
          },
        },
        series: [
          {
            name: "Data",
            data: [],
          },
        ],
      },

      barChartOptions: {
        chart: {
          type: "column",
          height: "320",
        },
        title: {
          text: "",
        },
        xAxis: {
          categories: [],
          crosshair: true,
        },
        yAxis: {
          min: 0,
          title: {
            text: "",
          },
        },
        tooltip: {
          headerFormat:
            '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat:
            '<tr><td style="padding:0"><b>{point.y:.2f}</b></td></tr>',
          footerFormat: "</table>",
          shared: true,
          useHTML: true,
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0,
          },
        },
        legend: {
          enabled: false,
        },
        series: [
          {
            data: [],
          },
        ],
      },
    };
  },
  mounted() {
    if (!this.data.length) {
      return;
    }

    const dataSet = this.data.map(({ source, revenue, color }) => ({
      source,
      revenue: parseFloat(revenue),
      color,
    }));

    // Update series and colors for the chart
    this.pieChartOptions.series[0].data = dataSet.map((data) => [
      data.source,
      data.revenue,
    ]);

    this.pieChartOptions.colors = this.colors;
    // Update xAxis categories and series for the bar chart
    this.barChartOptions.xAxis.categories = dataSet.map((data) => data.source);
    this.barChartOptions.series[0].data = dataSet.map((data) => ({
      y: data.revenue,
      color: data.color,
    }));

    this.loading = false;
  },

  computed: {
    totalRooms() {
      return this.data.reduce(
        (sum, item) => sum + parseFloat(item.no_of_room),
        0
      );
    },
    totalRevenue() {
      return this.data.reduce((sum, item) => sum + parseFloat(item.revenue), 0).toFixed(2);
    },
  },
};
</script>

<style scoped>
@import url("@/assets/css/tableStyles.css");
</style>
