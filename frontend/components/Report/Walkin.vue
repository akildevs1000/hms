<template>
  <v-row no-gutters>
      <v-col cols="7">
        <table class="mt-12">
          <thead>
            <tr>
              <td class="text-center">
                <small>COLOR</small>
              </td>
              <td><small>NAME</small></td>
              <td class="text-center"><small>Phone Number</small></td>
              <td class="text-center"><small>Number of Visit</small></td>
              <td class="text-center"><small>NO OF ROOM</small></td>
              <td class="text-center"><small>REVENUE</small></td>
              <td class="text-center"><small>PERCENTAGE %</small></td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in tableData" :key="index">
              <td class="text-center">
                <v-icon :style="{ color: item.color, fontSize: '24px' }"
                  >mdi-circle</v-icon
                >
              </td>
              <td>{{ item.source }}</td>
              <td class="text-center">{{ item.phone_number }}</td>
              <td class="text-center">{{ item.visit_count }}</td>
              <td class="text-center">{{ item.no_of_room }}</td>
              <td class="text-center">{{ item.revenue.toFixed(2) }}</td>
              <td class="text-center">{{ item.percentage }}</td>
            </tr>
          </tbody>
        </table>
      </v-col>
      <v-col cols="5" class="text-center">
        <!-- <highcharts :options="pieChartOptions"></highcharts> -->
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
      default: "Walk in customer Top 10 Rates Chart",
    },
  },

  data() {
    return {
      loading: true,
      selectedDate: null,
      dateOptions: [
        { text: "Today", value: "today" },
        { text: "Yesterday", value: "yesterday" },
        { text: "This week", value: "this_week" },
        { text: "This month", value: "this_month" },
        { text: "Customized date", value: "customized_date" },
      ],
      tableData: [
        {
          color: "red",
          source: "Saravan",
          phone_number: 8676145456,
          visit_count: 25,
          no_of_room: 39,
          percentage: "45.00%",
          revenue: 750960.0,
        },
        {
          color: "blue",
          source: "Kumar",
          phone_number: 978434411,
          visit_count: 15,
          no_of_room: 30,
          percentage: "22.50%",
          revenue: 750960.0,
        },
        {
          color: "purple",
          source: "Fahad",
          phone_number: 122225675,
          visit_count: 12,
          no_of_room: 24,
          percentage: "12.65%",
          revenue: 454667.0,
        },
        {
          color: "orange",
          source: "Senthil",
          phone_number: 68654425,
          visit_count: 10,
          no_of_room: 22,
          percentage: "7.23%",
          revenue: 565658.0,
        },
        {
          color: "grey",
          source: "Karupan",
          phone_number: 67565452,
          visit_count: 8,
          no_of_room: 21,
          percentage: "5.14%",
          revenue: 45454.0,
        },
        {
          color: "green",
          source: "Francis",
          phone_number: 57567822,
          visit_count: 7,
          no_of_room: 50,
          percentage: "5.00%",
          revenue: 57567.0,
        },
      ],
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
    // Prepare data for chart
    const dataSet = this.tableData.map(({ source, revenue, color }) => ({
      source,
      revenue,
      color,
    }));

    // Update series and colors for the chart
    this.pieChartOptions.series[0].data = dataSet.map((data) => [
      data.source,
      data.revenue,
    ]);
    this.pieChartOptions.colors = dataSet.map((data) => data.color);

    // Update xAxis categories and series for the bar chart
    this.barChartOptions.xAxis.categories = dataSet.map((data) => data.source);
    this.barChartOptions.series[0].data = dataSet.map((data) => ({
      y: data.revenue,
      color: data.color,
    }));

    // Finish loading
    this.loading = false;
  },

  computed: {
    // totalRooms() {
    //   return this.tableData.reduce((sum, item) => sum + item.no_of_room, 0);
    // },
    // totalRevenue() {
    //   return this.tableData.reduce((sum, item) => sum + item.revenue, 0);
    // },
  },
};
</script>

<style scoped>
@import url("@/assets/css/tableStyles.css");
</style>
