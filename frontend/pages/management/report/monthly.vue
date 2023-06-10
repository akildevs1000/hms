<template>
  <div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="3">
        <v-select :items="months" label="Select Month" outlined dense item-value="id" item-text="name" v-model="month"
          @change="getReportByMonth(month)"></v-select>
      </v-col>
    </v-row>

    <div v-if="can(`agents_view`)">
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-tabs v-model="activeTab" :vertical="vertical" background-color="primary" dark show-arrows>
          <v-spacer></v-spacer>
          <v-tab active-class="active-link">
            <v-icon> mdi mdi-chart-bar </v-icon>
          </v-tab>
          <v-tab active-class="active-link">
            <v-icon> mdi mdi-chart-pie </v-icon>
          </v-tab>
          <v-tab active-class="active-link">
            <!-- <v-icon> mdi mdi-chart-pie </v-icon> -->
            source
          </v-tab>
          <v-tabs-slider color="#1259a7"></v-tabs-slider>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <client-only>
                  <ApexCharts :options="barChartOptions" :series="barSeries" chart-id="bar" :height="400"
                    :key="chartKey" />
                </client-only>
              </v-card-text>
            </v-card>
          </v-tab-item>

          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <client-only>
                  <ApexCharts :options="chartOptions" :series="series" :height="400" chart-id="pieChart"
                    :key="chartKey" />
                </client-only>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <client-only>
                  <SourceReport :parentMonth="month" />
                </client-only>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </v-card>
    </div>
  </div>
</template>

<script>
import SourceReport from '../../../components/bookingSource/SourceReport.vue';
export default {
  components: {
    SourceReport,
  },
  data() {
    return {
      series: [],
      chartOptions: {
        chart: {
          width: 380,
          type: "pie",
        },
        labels: ["Sold", "Unsold"],
        colors: ["#228B22", "#D71921"], // set custom colors

        plotOptions: {
          pie: {
            dataLabels: {
              offset: -5,
            },
          },
        },
        legend: {
          show: false,
        },
        dataLabels: {
          formatter(val, opts) {
            const name = opts.w.globals.labels[opts.seriesIndex];
            return [name, val.toFixed(1) + "%"];
          },
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                width: 200,
              },
              legend: {
                position: "bottom",
              },
            },
          },
        ],
      },
      // -------------------end pie chart ----------------

      barSeries: [
        {
          name: "Sold",
          data: [],
        },
      ],

      barChartOptions: {
        chart: {
          type: "bar",
          height: 350,
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "55%",
            endingShape: "rounded",
          },
        },
        colors: ["#228B22", "#D71921"], // set custom colors
        dataLabels: {
          enabled: false,
        },
        stroke: {
          show: true,
          width: 2,
          colors: ["transparent"],
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          title: {
            text: "% (Percentage)",
          },
          labels: {
            show: true,
            formatter: function (val) {
              return val + "%";
            },
          },
        },

        fill: {
          opacity: 1,
        },
        tooltip: {
          x: {
            formatter: function (val) {
              return "% " + val + " Percentage";
            },
          },
        },
      },

      // -------------------end bar chart ----------------

      Model: "Report",
      endpoint: "get_occupancy_rate_by_month",
      from_date: "",
      from_menu: false,
      loading: false,
      to_date: "",
      to_menu: false,

      vertical: false,
      activeTab: 0,

      chartKey: 0,
      month: "",
      months: [
        { id: 1, name: "January" },
        { id: 2, name: "February" },
        { id: 3, name: "March" },
        { id: 4, name: "April" },
        { id: 5, name: "May" },
        { id: 6, name: "June" },
        { id: 7, name: "July" },
        { id: 8, name: "August" },
        { id: 9, name: "September" },
        { id: 10, name: "October" },
        { id: 11, name: "November" },
        { id: 12, name: "December" },
      ],

      options: {},
      data: [],
      headers: [
        {
          text: "#",
        },
        {
          text: "Date",
        },
        {
          text: "Sold (%)",
        },
        {
          text: "Unsold (%)",
        },
      ],
    };
  },

  created() {
    this.loading = true;
  },
  mounted() {
    // this.getDataFromApi();
    this.month = new Date().getMonth() + 1;
    this.getReportByMonth(this.month);
  },

  methods: {
    onPageChange() {
      this.getDataFromApi();
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    commonMethod() {
      this.getDataFromApi();
    },

    getReportByMonth(month) {
      this.getDataFromApi();
      this.getDaysInMonth(month);
    },

    getDaysInMonth(month = 2, year = new Date().getFullYear()) {
      const date = new Date(year, month, 0);
      let d = date.getDate();

      this.barChartOptions.xaxis.categories.splice(
        0,
        this.barChartOptions.xaxis.categories.length
      );

      for (let i = 1; i <= d; i++) {
        this.barChartOptions.xaxis.categories.push(i);
      }
      this.forceChartRerender();
    },

    forceChartRerender() {
      this.chartKey += 1;
    },

    getDataFromApi(url = this.endpoint) {
      this.barSeries[0]["data"].splice(0, this.barSeries[0]["data"].length);
      this.series.splice(0, this.series.length);

      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          month: this.month,
        },
      };
      this.$axios.get(`${url}`, options).then(({ data }) => {
        data.sold.forEach((item) => this.barSeries[0]["data"].push(item));

        let totSold = eval(data.sold.join("+"));
        let totUnsold = eval(data.unsold.join("+"));
        this.series.push(...[totSold, totUnsold]);
        this.forceChartRerender();
        this.loading = false;
      });
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}
</style>
