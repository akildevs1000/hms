<template>
  <div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>

    <v-row>
      <!-- <v-col md="3">
        <v-menu
          v-model="from_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="from_date"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              :hide-details="true"
              class="custom-text-box shadow-none"
              solo
              flat
              label="From"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="from_date"
            @input="from_menu = false"
            @change="commonMethod"
          ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
        <v-menu
          v-model="to_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="to_date"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              class="custom-text-box shadow-none"
              solo
              flat
              label="To"
              :hide-details="true"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="to_date"
            @input="to_menu = false"
            @change="commonMethod"
          ></v-date-picker>
        </v-menu>
      </v-col> -->

      <v-col md="3">
        <v-select
          :items="months"
          label="Select Month"
          outlined
          dense
          item-value="id"
          item-text="name"
          v-model="month"
          @change="getReportByMonth(month)"
        ></v-select>
      </v-col>
    </v-row>

    <div v-if="can(`agents_view`)">
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span> {{ Model }} List</span>
        </v-toolbar>
        <client-only>
          <!-- <ApexCharts
            :options="chartOptions"
            :series="series"
            :width="400"
            :height="400"
            chart-id="pieChart"
          /> -->
          <!-- <pre>{{ barChartOptions.xaxis.categories }}</pre> -->
          <!-- {{ barSeries }} -->
          <ApexCharts
            :options="barChartOptions"
            :series="barSeries"
            chart-id="bar"
            :height="400"
            :key="chartKey"
          />
        </client-only>
      </v-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      series: [44, 55],
      chartOptions: {
        chart: {
          width: 380,
          type: "pie",
        },
        labels: ["Sold", "Unsold"],

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

      chartKey: 0,
      month: 1,
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
    this.getReportByMonth(1);
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

      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          month: this.month,
        },
      };
      this.$axios.get(`${url}`, options).then(({ data }) => {
        data.sold.forEach((item) => this.barSeries[0]["data"].push(item));
        console.log(this.barSeries[0]["data"]);
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
