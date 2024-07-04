<template>
  <div class="mt-5">
    <v-row no-gutters>
      <v-col cols="9"> {{ heading }} </v-col>
      <v-col cols="3">
        <v-autocomplete
          v-model="selectedDate"
          :items="dateOptions"
          label="Select Date"
          item-text="text"
          item-value="value"
          outlined
          dense
          class="red-text"
        ></v-autocomplete>
      </v-col>
      <v-col cols="7">
        <table class="mt-12">
          <thead>
            <tr>
              <td class="text-center"><small>COLOR</small></td>
              <td><small>Month</small></td>
              <td class="text-center"><small>Room sold</small></td>
              <td class="text-center"><small>Income</small></td>
              <td class="text-center"><small>Expenses</small></td>
              <td class="text-center"><small>Management Expense</small></td>
              <td class="text-center"><small>Profit</small></td>
              <td class="text-center"><small>%</small></td>
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
              <td class="text-center">{{ item.no_of_room }}</td>
              <td class="text-center">{{ item.income.toFixed(2) }}</td>
              <td class="text-center">{{ item.expense }}</td>
              <td class="text-center">{{ item.management_expense }}</td>
              <td class="text-center">{{ item.profit }}</td>
              <td class="text-center">{{ item.percentage }}</td>
            </tr>
            <tr>
              <td colspan="2">TOTAL</td>
              <td class="text-center">{{ totalRooms }}</td>
              <td class="text-center">4519785.00</td>
              <td class="text-center">77876.00</td>
              <td class="text-center">132428.00</td>
              <td class="text-center">5645335</td>
              <td class="text-center">100%</td>

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
  </div>
</template>

<script>
export default {
  props: {
    heading: {
      default: "Month Wise Report",
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
          source: "Jan 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
        },
        {
          color: "blue",
          source: "Feb 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
        },
        {
          color: "purple",
          source: "March 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
        },
        {
          color: "orange",
          source: "April 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
        },
        {
          color: "grey",
          source: "May 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
        },
        {
          color: "yellow",
          source: "June 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
        },
        {
          color: "pink",
          source: "July 23",
          no_of_room: 134,
          income: 850000,
          expense: 65655,
          management_expense: 15750,
          profit: 45677,
          percentage: "45.00%",
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
    const dataSet = this.tableData.map(({ source, income, color }) => ({
      source,
      income,
      color,
    }));

    // Update series and colors for the chart
    this.pieChartOptions.series[0].data = dataSet.map((data) => [
      data.source,
      data.income,
    ]);
    this.pieChartOptions.colors = dataSet.map((data) => data.color);

    // Update xAxis categories and series for the bar chart
    this.barChartOptions.xAxis.categories = dataSet.map((data) => data.source);
    this.barChartOptions.series[0].data = dataSet.map((data) => ({
      y: data.income,
      color: data.color,
    }));

    // Finish loading
    this.loading = false;
  },

  computed: {
    totalRooms() {
      return this.tableData.reduce((sum, item) => sum + item.no_of_room, 0);
    },
    totalRevenue() {
      return this.tableData.reduce((sum, item) => sum + item.income, 0);
    },
  },
};
</script>

<style scoped>
@import url("@/assets/css/tableStyles.css");
</style>
