<template>
  <div v-if="can('management_custom_soldout_access') && can('management_custom_soldout_view')">
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>

    <v-row>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-select class="custom-text-box shadow-none" v-model="filterType" :items="[
          {
            id: 1,
            name: 'Today',
          },
          {
            id: 2,
            name: 'Yesterday',
          },
          {
            id: 3,
            name: 'This Week',
          },
          {
            id: 4,
            name: 'This Month',
          },
          {
            id: 5,
            name: 'Custom',
          },
        ]" dense placeholder="Type" solo flat :hide-details="true" item-text="name" item-value="id"
          @change="getDataFromApi()"></v-select>
      </v-col>

      <v-col md="3" v-if="filterType == 5">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
              class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3" v-if="filterType == 5">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense class="custom-text-box shadow-none"
              solo flat label="To" :hide-details="true"></v-text-field>
          </template>
          <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>

    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> Day Report</span>
      </v-toolbar>
      <client-only>
        <ApexCharts :options="chartOptions" :series="series" :height="400" chart-id="pieChart" :key="chartKey" />
      </client-only>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data() {
    return {
      series: [],
      chartOptions: {
        chart: {
          width: 380,
          type: "pie",
        },
        labels: ["Sold", "Unsold"],
        colors: ["#00b300", "#ff3333"], // set custom colors
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
      // -------------------end chart ----------------

      Model: "Report",
      endpoint: "get_occupancy_rate_by_filter",
      chartKey: 0,

      from_date: "",
      from_menu: false,

      to_date: "",
      to_menu: false,
      loading: false,

      pagination: {
        current: 1,
        total: 0,
        per_page: 10,
      },
      options: {},
      filterType: 1,
      data: [],
      headers: [
        {
          text: "#",
        },
        {
          text: "Date",
        },
        {
          text: "Booking Date",
        },
        {
          text: "Booking Date",
        },
      ],
    };
  },

  created() {
    this.loading = true;
  },
  mounted() {
    this.getDataFromApi();
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    commonMethod() {
      if (this.from_date && this.to_date) {
        this.getDataFromApi();
      }
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      this.series = [];
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
          filterType: this.filterType,
        },
      };
      this.$axios.get(url, options).then(({ data }) => {
        const { sold, unsold } = data;
        let totSold = eval(sold.join("+"));
        let totUnsold = eval(unsold.join("+"));
        this.series.push(...[totSold, totUnsold]);
        this.forceChartRerender();
        this.loading = false;
      });
    },

    forceChartRerender() {
      this.chartKey += 1;
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
