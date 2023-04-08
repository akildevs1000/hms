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
              label="Date"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="from_date"
            @input="from_menu = false"
            @change="commonMethod"
          ></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
    <!-- {{ data }} -->
    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> Day Report</span>
      </v-toolbar>
      <client-only>
        <ApexCharts
          :options="chartOptions"
          :series="series"
          :height="400"
          chart-id="pieChart"
          :key="chartKey"
        />
      </client-only>
    </v-card>

    <!-- <div v-if="can(`agents_view`)">
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span> {{ Model }} List</span>
        </v-toolbar>
        <table>
          <tr style="font-size: 12px">
            <th v-for="(item, index) in headers" :key="index">
              {{ item.text }}
            </th>
          </tr>
          <v-progress-linear
            v-if="loading"
            :active="loading"
            :indeterminate="loading"
            absolute
            color="primary"
          ></v-progress-linear>

          <tr
            v-for="(item, index) in data"
            :key="index"
            style="font-size: 12px"
          >
            <td>
              <b>{{ ++index }}</b>
            </td>
            <td>{{ item.date || "---" }}</td>
            <td>
              <v-chip small class="ma-2" color="green" text-color="white">
                {{ item.sold }} %
              </v-chip>
            </td>
            <td>
              <v-chip small class="ma-2" color="red" text-color="white">
                {{ item.unsold }} %
              </v-chip>
            </td>
          </tr>
        </table>
      </v-card>
      <div>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination
                v-model="pagination.current"
                :length="pagination.total"
                @input="onPageChange"
                :total-visible="12"
              ></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div> -->
  </div>
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
      endpoint: "get_single_day_occupancy_rate",
      chartKey: 0,

      from_date: new Date().toJSON().slice(0, 10),
      from_menu: false,
      loading: false,

      pagination: {
        current: 1,
        total: 0,
        per_page: 10,
      },
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

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      this.series = [];
      let page = this.pagination.current;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          date: this.from_date,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.record;
        if (!this.data) {
          alert("data not found");
          return;
        }
        const { sold, unsold } = data.record;
        this.series.push(...[sold, unsold]);
        this.forceChartRerender();
        this.loading = false;
      });
    },

    forceChartRerender() {
      this.chartKey += 1;
    },

    searchIt() {
      if (this.search.length == 0) {
        this.getDataFromApi();
      } else if (this.search.length > 2) {
        this.getDataFromApi();
      }
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
