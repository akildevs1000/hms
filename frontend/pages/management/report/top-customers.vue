<template>
  <div v-if="can('management_top_10_customers_access') && can('management_top_10_customers_view')">

    <v-row>

      <!-- <v-col md="2">
        <v-select :items="years" label="Select Year" outlined dense v-model="year" @change="getDataFromApi()"></v-select>
      </v-col>
      <v-col md="2">
        <v-select :items="months" label="Select Month" outlined dense item-value="id" item-text="name" v-model="month"
          @change="getDataFromApi()"></v-select>
      </v-col> -->
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
      <v-card class="mb-5" elevation="0">
        <v-toolbar class="rounded-md mb-2 white--text" color="background" dense flat>
          <v-col cols="12">
            <span>Top 10 walk-in Customers </span>


            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
                  <v-icon color="white" class="ml-2" @click="getDataFromApi" dark>mdi mdi-reload</v-icon>
                </v-btn>
              </template>
              <span>Reload</span>
            </v-tooltip>
          </v-col>
        </v-toolbar>
        <v-row>
          <v-col cols="8">

            <v-data-table dense :headers="headers_table" :items="data_table" :loading="loading" :footer-props="{
              itemsPerPageOptions: [12],
            }" class="elevation-1" :hide-default-footer="true">



              <template v-slot:item.name="{ item }">
                <a @click="getToCheckoutPage(item)"> {{ item.title }}</a> </template>

              <template v-slot:item.phone_number="{ item }">
                {{ item.customer.contact_no }}
              </template>
              <template v-slot:item.no_of_visits="{ item }">
                {{ item.number_of_visits }}
              </template>
              <template v-slot:item.no_of_rooms="{ item }">
                {{ getRoomsCount(item.rooms) }}
              </template>
              <template v-slot:item.revenue="{ item }">
                {{ getPriceFormat(item.customer_total_price) }}
              </template>

              <template v-slot:item.percentage="{ item }">
                {{ getPercentage(item.customer_total_price) }} %
              </template>
              <template slot="body.append">
                <tr>
                  <td class="text-right  font-weight-bold" colspan="3">TOTAL</td>
                  <td class="text-right font-weight-bold">{{ total_visits }}</td>
                  <td class="text-right font-weight-bold">{{ total_rooms }}</td>
                  <td class="text-right font-weight-bold">{{ getPriceFormat(total_price) }}</td>
                  <td>&nbsp;</td>
                </tr>
              </template>

            </v-data-table>

          </v-col>
          <v-col cols="4">
            <!-- <ApexCharts v-model="chart" ref="realtimeChart" :options="barChartOptions" :series="barSeries" chart-id="bar"
              :height="400" :key="chartKey" /> -->

            <ApexCharts :options="chartOptions" ref="realtimeChart" :series="series" :height="400" chart-id="pieChart"
              :key="chartKey" />
          </v-col>
        </v-row>

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
      total_rooms: 0,
      total_visits: 0,
      total_price: 0,
      data_table: [],
      grandTotal: [],
      totalRowsCount: 0,
      series: [],
      colors: [],

      series: [],

      chartOptions: {
        chart: {
          width: 380,
          type: "pie",
        },
        labels: [],// ["Sold", "Unsold"],
        colors: [],// ["#228B22", "#D71921"], // set custom colors
        customLabel: [],
        // plotOptions: {
        //   pie: {
        //     dataLabels: {
        //       offset: -5,
        //     },
        //   },
        // },
        legend: {
          show: false,
        },
        dataLabels: {
          formatter(val, opts) {
            const name = opts.w.globals.labels[opts.seriesIndex];
            return [name, val.toFixed(1) + "%"];
          },
        },

        tooltip: {
          enabled: true,
          y: {
            formatter: function (val, opts) {
              return opts.config.customLabel[opts.seriesIndex]
            },
            title: {
              formatter: function (seriesName) {
                return ''
              }
            }
          }
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
      barChartOptions: {

        chart: {
          type: "bar",
          id: 'basic-bar'

        },
        xaxis: {
          categories: []
        },
        yaxis: {
          max: 100 // Set the maximum value of the y-axis to 100
        },

        plotOptions: {
          bar: {
            distributed: true,
            dataLabels: {
              position: 'top', // Set the position of the labels on top of the bars
              formatter: function (val) {
                return val + '111 %'; // Customize the label format, e.g., add a percentage sign
              }
            }
          }
        },
        colors: [],
        legend: {
          show: false
        },
      },
      series: [{
        name: 'series-1',
        data: []//[30, 40, 45, 50, 49, 60, 70, 91]

      }],

      Model: "Report",
      endpoint: "get_occupancy_rate_by_month",
      from_date: "",
      from_menu: false,
      loading: false,
      to_date: "",
      to_menu: false,

      vertical: false,
      activeTab: 0,
      chart: {},
      chartKey: 0,
      year: "",
      years: "",
      month: "",
      months: [
        { id: '', name: "All Months" },
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
      headers_table: [

        {
          text: "Name",
          align: "left",
          sortable: false,
          filterable: false,
          value: "name",
        },
        {
          text: "Phone Number",
          align: "right",
          sortable: false,

          filterable: false,
          value: "phone_number",
        },
        {
          text: "No.of Visits",
          align: "right",
          sortable: false,

          filterable: false,
          value: "no_of_visits",
        },
        {
          text: "No.of Rooms",
          align: "right",
          sortable: false,
          key: "employee_id",
          filterable: false,
          value: "no_of_rooms",
        },
        {
          text: "Revenue",
          align: "right",
          sortable: false,

          filterable: false,
          value: "revenue",
        },
        {
          text: "%",
          align: "right",
          sortable: false,

          filterable: false,
          value: "percentage",
        },

      ],

    };
  },

  created() {
    this.loading = true;

    this.getYears();
    //this.month = new Date().getMonth() + 1;
    //this.year = new Date().getFullYear();

    this.month = new Date().getMonth();
    this.year = new Date().getFullYear();

    this.filter_from_date = this.formatDate(new Date(this.year, 0, 1));
    this.filter_to_date = this.formatDate(new Date(this.year, this.month + 1, 0));

    this.getDataFromApi();
  },
  mounted() {
    // this.getDataFromApi();

  },
  // watch: {

  //   options: {
  //     handler() {
  //       this.getMonthlyWiseData();
  //     },
  //     deep: true,
  //   },
  // },
  methods: {
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();

      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    getToCheckoutPage(item) {
      this.$store.dispatch('setData', { customer_name: item.first_name });
      this.$router.push('reservation/check_out');
    },
    getPriceFormat(amount) {

      amount = parseFloat(amount);
      return amount.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    },
    getPercentage(customer_total_price) {
      if (this.total_price > 0) return Math.round((customer_total_price / this.total_price) * 100);
      else return 0;
    },
    getRoomsCount(rooms) {
      return rooms.split(",").length;
    },
    getColorCode(index) {
      return this.colors[index].color;
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

    getDaysInMonth(month = 2, year = new Date().getFullYear()) {

    },

    forceChartRerender() {
      this.chartKey += 1;
    },
    getYears() {
      const year = new Date().getFullYear();
      this.years = Array.from({ length: 10 }, (_, i) => year - i);

    },
    getDataFromApi(url = this.endpoint) {


      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";


      this.loading = true;
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          month: this.month,
          year: this.year,
          filter_from_date: this.filter_from_date,
          filter_to_date: this.filter_to_date,

        },
      };



      this.$axios.get('get_report_top-ten-customers', options).then(({ data }) => {

        this.data_table = data.data;
        this.total_price = data.total_price;
        this.colors = data.colors;
        this.loading = false;
        this.totalRowsCount = 12;
        this.grandTotal = data.grandTotal;

        this.series.splice(0, this.series.length);


        this.total_rooms = 0;
        this.total_visits = 0;
        let counter = 0;
        this.data_table.forEach(item => {

          let rooms = this.getRoomsCount(item.rooms);
          this.series.push(Math.round((item.customer_total_price / data.total_price) * 100));
          this.chartOptions.labels[counter] = item.title;
          this.chartOptions.customLabel[counter] = item.title + "<br/>Total Amount: " + this.getPriceFormat(item.customer_total_price) + "<br/>No.of Visits: " + item.number_of_visits + "<br/>No.of Rooms: " + rooms;

          this.chartOptions.colors[counter] = data.colors[counter].color;

          this.total_rooms = this.total_rooms + rooms;
          this.total_visits += item.number_of_visits;
          counter++;

        });

        this.loading = false;
        this.loading = false;

      });
    },
  },
};
</script>
