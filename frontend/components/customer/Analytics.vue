<template>
  <v-container
    ><style>
      .text-field-customer .v-input__slot {
        padding: 0px 5px !important;
      }
      .text-field-customer .v-input__slot {
        min-height: 30px !important;
      }
      .text-field-customer .v-label {
        line-height: 11px !important;
      }
      .text-field-customer .v-input__icon {
        height: 17px !important;
      }
    </style>
    <v-row>
      <v-col cols="12">
        <v-tabs right>
          <div class="pt-1">
            <AssetsPickerMonthly @months="CustomFilter" />
          </div>
          <v-spacer></v-spacer>
          <v-tab>statistical </v-tab>
          <v-tab>pie chart </v-tab>
          <v-tab-item class="mt-1">
            <v-card outlined class="pa-5">
              <highcharts :options="barChartOptions"></highcharts>
            </v-card>
          </v-tab-item>
          <v-tab-item class="mt-1">
            <v-card outlined class="pa-5">
              <highcharts :options="pieChartOptions"></highcharts>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
const getFirstAndLastMonth = () => {
  const fullYear = new Date().getFullYear();
  return [`${fullYear}-01`, `${fullYear}-12`];
};

export default {
  props: ["customerId"],
  data() {
    return {
      date: null,
      menu2: false,

      loading: true,
      stats: [],
      customer: "",
      bookings: [],
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      viewportWidth: 0,
      viewportHeight: 0,
      headers: [],
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
        colors: [
          "#33FF57",
          "#3357FF",
          "#FF33A1",
          "#FFA533",
          "#57FF33",
          "#33FFD1",
          "#FF5733",
          "#FFD133",
          "#5733FF",
          "#33FFB2",
          "#FF33B5",
          "#FF5733",
        ],

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
      months: getFirstAndLastMonth(),
    };
  },
  computed: {},
  mounted() {
    this.get_customer_history();

    this.viewportWidth = window.innerWidth;
    this.viewportHeight = window.innerHeight;

    window.addEventListener("resize", () => {
      this.viewportWidth = window.innerWidth;
      this.viewportHeight = window.innerHeight;
    });
  },
  methods: {
    CustomFilter(e) {
      this.months = e;
      this.get_customer_history();
    },
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    get_customer_history() {
      let config = {
        params: {
          from_date: this.months[0],
          to_date: this.months[1],
        },
      };
      this.$axios
        .get(`get_customer_analytics/${this.customerId}`, config)
        .then(({ data }) => {
          this.pieChartOptions.series[0].data = data.map((data) => [
            data.label,
            data.value,
          ]);

          // Update xAxis categories and series for the bar chart
          this.barChartOptions.xAxis.categories = data.map(
            (data) => data.label
          );

          this.barChartOptions.series[0].data = data.map((e) => ({
            y: e.value,
            color: "#71de36",
          }));

          this.loading = false;
        });
    },
  },
};
</script>
