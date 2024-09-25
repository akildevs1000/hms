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
      <!-- <v-col cols="12">
        <FilterDateRange height="30" @filter-attr="filterAttr" />
      </v-col> -->
      <v-col cols="12">
        <v-tabs right>
          <div class="py-3">
            <span>
              <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    class="text-field-customer"
                    dense
                    outlined
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    v-model="dates"
                    hide-details
                  ></v-text-field>
                </template>

                <v-date-picker
                  v-model="dates"
                  range
                  @input="CustomFilter"
                  no-title
                ></v-date-picker>
              </v-menu>
            </span>
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
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
const getFirstAndLastDateOfCurrentMonth = () => {
  const startDate = new Date();
  startDate.setDate(1); // Set to the 1st of the month

  const endDate = new Date(
    startDate.getFullYear(),
    startDate.getMonth() + 1,
    0
  ); // Get the last date of the month

  return [
    startDate.toISOString().substr(0, 10), // First date
    endDate.toISOString().substr(0, 10), // Last date
  ];
};

export default {
  components: {
    DatePicker,
  },
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
      dates: getFirstAndLastDateOfCurrentMonth(),
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
    CustomFilter() {
      if (this.dates[0] && this.dates[1]) {
        this.get_customer_history();
        this.menu2 = false;
      }
    },
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    get_customer_history() {
      let config = {
        params: {
          from_date: this.dates[0],
          to_date: this.dates[1],
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
