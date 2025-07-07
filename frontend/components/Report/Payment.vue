<template>
  <v-row no-gutters>
    <style scoped>
      td {
        padding: 5px;
      }
    </style>
    <v-col cols="6">
      <table class="mt-12" border="1">
        <thead>
          <tr>
            <td class="text-center">
              <small>COLOR</small>
            </td>
            <td><small>Payment</small></td>
            <td class="text-center"><small>PERCENTAGE %</small></td>
            <td class="text-center"><small>REVENUE</small></td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in tableData" :key="index">
            <td class="text-center">
              <v-icon :style="{ color: item.color, fontSize: '20px' }"
                >mdi-circle</v-icon
              >
            </td>
            <td>{{ item.source }}</td>
            <td class="text-center">{{ item.percentage }}</td>
            <td class="text-center">{{ item.revenue.toFixed(2) }}</td>
          </tr>
          <tr>
            <td colspan="2">TOTAL</td>
            <td class="text-center">100%</td>
            <td class="text-center">{{ totalRevenue.toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </v-col>
    <v-col cols="6" class="text-center">
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
  props: ["filters"],

  data() {
    return {
      loading: true,
      selectedDate: null,
      tableData: [],
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
  watch: {
    filters: {
      handler(data) {
        this.fetchPaymentReport();
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    async fetchPaymentReport() {
      this.loading = true;

      try {

        const response = await this.$axios.get("/report-by-payment", {
          params: this.filters,
        });

        this.tableData = response.data;

        // Prepare data for chart
        const dataSet = this.tableData.map(({ source, revenue, color }) => ({
          source,
          revenue,
          color,
        }));

        // Update pie chart
        this.pieChartOptions.series[0].data = dataSet.map((d) => [
          d.source,
          d.revenue,
        ]);
        this.pieChartOptions.colors = dataSet.map((d) => d.color);

        // Update bar chart
        this.barChartOptions.xAxis.categories = dataSet.map((d) => d.source);
        this.barChartOptions.series[0].data = dataSet.map((d) => ({
          y: d.revenue,
          color: d.color,
        }));
      } catch (error) {
        console.error("Error fetching report-by-payment:", error);
      } finally {
        this.loading = false;
      }
    },
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
    totalRevenue() {
      return this.tableData.reduce((sum, item) => sum + item.revenue, 0);
    },
  },
};
</script>
