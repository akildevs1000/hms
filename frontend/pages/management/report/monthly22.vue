<template>
  <div v-if="can('management_soldout_access') && can('management_soldout_view')">
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>Soldout {{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="3">
        <v-select :items="months" label="Select Month" outlined dense item-value="id" item-text="name" v-model="month"
          @change="getReportByMonth(month)"></v-select>
      </v-col>
      <v-col md="2">
        <v-menu ref="menu_from_filter" v-model="menu_from_filter" :close-on-content-click="false"
          transition="scale-transition" offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field outlined dense v-model="filter_from_date" readonly v-bind="attrs" v-on="on"
              label="From Date"></v-text-field>
          </template>
          <v-date-picker style="height: 400px" v-model="filter_from_date" no-title scrollable
            @input="getDataFromApi(); menu_from_filter = false">


          </v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="2">
        <v-menu ref="menu_to_filter" v-model="menu_to_filter" :close-on-content-click="false"
          transition="scale-transition" offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field outlined dense v-model="filter_to_date" readonly v-bind="attrs" v-on="on"
              label="To Date"></v-text-field>
          </template>
          <v-date-picker style="height: 400px" v-model="filter_to_date" no-title scrollable
            @input="getDataFromApi(); menu_to_filter = false">


          </v-date-picker>
        </v-menu>

      </v-col>
    </v-row>

    <div>
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
            Revenue Sources
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
                  <ApexCharts :options="pieChartOptions" :series="pieSeries" :height="400" chart-id="pieChart"
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
  <NoAccess v-else />
</template>

<script>
import SourceReport from '../../../components/bookingSource/SourceReport.vue';
export default {
  components: {
    SourceReport,
  },
  data() {
    return {
      menu_from_filter: '',
      filter_from_date: '',
      menu_to_filter: '',
      filter_to_date: '',
      pieSeries: [],
      pieChartOptions: {
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
            return [name, val.toFixed(1)];
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
        title: {
          text: 'Daily Wise Report',
        },
        customLabel: [],
        chart: {
          type: "area",
          id: 'DailyReport'

        },
        colors: ['#0C9241', '#FF0000'],

        plotOptions: {
          bar: {
            horizontal: false,
            dataLabels: {
              position: 'top',
            },
          }
        },
        dataLabels: {
          enabled: false,

          style: {
            fontSize: '12px',
            colors: ['#fff']
          }
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['#fff']
        },
        tooltip: {
          shared: true,
          intersect: false
        },
        xaxis: {
          categories: []
        },
        tooltip: {
          enabled: true,
          y: {
            formatter: function (val, opts) {


              return opts.w.config.customLabel[opts.dataPointIndex]
            },
            title: {
              formatter: function (seriesName) {
                return ''
              }
            }
          }
        },
      },

      // -------------------end bar chart ----------------

      Model: "Report",
      endpoint: "get_occupancy_rate_by_month2",
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
    this.month = new Date().getMonth();
    this.year = new Date().getFullYear();
    this.filter_from_date = this.formatDate(new Date(this.year, this.month, 1));
    this.filter_to_date = this.formatDate(new Date(this.year, this.month + 1, 0));
  },
  mounted() {
    // this.getDataFromApi();
    this.month = new Date().getMonth() + 1;
    this.getReportByMonth(this.month);
  },

  methods: {
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();

      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    onPageChange() {
      this.getDataFromApi();
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
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
      // const date = new Date(year, month, 0);
      // let d = date.getDate();

      // this.barChartOptions.xaxis.categories.splice(
      //   0,
      //   this.barChartOptions.xaxis.categories.length
      // );

      // for (let i = 1; i <= d; i++) {
      //   this.barChartOptions.xaxis.categories.push(i);
      // }
      // this.forceChartRerender();
    },

    forceChartRerender() {
      this.chartKey += 1;
    },

    getDataFromApi(url = this.endpoint) {
      //this.barSeries[0]["data"].splice(0, this.barSeries[0]["data"].length);
      //this.pieSeries.splice(0, this.pieSeries.length);

      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          month: this.month,
          filter_from_date: this.filter_from_date,
          filter_to_date: this.filter_to_date,
        },
      };
      this.$axios.get(`${url}`, options).then(({ data }) => {
        data.sold.forEach((item) => this.barSeries[0]["data"].push(item));

        let totSold = eval(data.sold.join("+"));
        let totUnsold = eval(data.unsold.join("+"));
        this.pieSeries.push(...[totSold, totUnsold]);
        this.forceChartRerender();
        this.loading = false;

      });
    },
  },
};
</script>
<!--
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
</style> -->
