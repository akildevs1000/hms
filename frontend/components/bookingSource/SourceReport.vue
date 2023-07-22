<template>
  <div>

    <div v-if="can(`agents_view`)">
      <v-card flat>
        <v-card-text>
          <v-row>
            <v-col md="6" cols="12">
              <table>
                <tr>
                  <th>Source</th>
                  <th class="text-right">Total Revenue</th>
                </tr>
                <tr v-for="(value, key) in data" :key="key">
                  <th>{{ key }}</th>
                  <td class="text-right">{{ convert_decimal(value) }}</td>
                </tr>
              </table>
            </v-col>

            <v-col md="6" cols="12">
              <client-only>
                <ApexCharts :options="chartOptions" :series="series" :height="500" chart-id="pieChart" :key="chartKey" />
              </client-only>
            </v-col>
          </v-row>

        </v-card-text>
      </v-card>
    </div>
  </div>
</template>

<script>
export default {
  props: ['parentMonth'],
  data() {
    return {
      series: [],
      chartOptions: {
        chart: {
          width: 380,
          type: "pie",
        },
        // labels: ["Sold room", "Unsold"],
        labels: [], //["Sold", "Unsold"]
        colors: ['#84b3ea', '#dd481c', '#1d7a8a', '#3096e9', '#1ea242', '#c1857b', '#71b98e', '#83b', '#ac8a98'], // set custom colors

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
          // formatter(val, opts) {
          //   const name = opts.w.globals.labels[opts.seriesIndex];
          //   console.log('name' + name);
          //   return [name, val.toFixed(1) + "%"];
          // },
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

      Model: "Report",
      endpoint: "get_source_rate_by_month",
      from_date: "",
      from_menu: false,
      loading: false,
      to_date: "",
      to_menu: false,
      month: this.parentMonth,
      vertical: false,
      activeTab: 0,

      chartKey: 0,
      // month: "",
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

  watch: {
    parentMonth() {
      this.getDataFromApi();
      console.log('watch');
    }
  },

  created() {
    this.loading = true;
  },
  mounted() {
    this.getDataFromApi();
  },

  methods: {

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },


    can(per) {
      return true;
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
      this.series.splice(0, this.series.length);

      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          month: this.parentMonth,
        },
      };

      this.$axios.get(`${url}`, options).then(({ data }) => {
        this.data = data;
        const keys = Object.keys(data);
        const values = Object.values(data);
        this.series = values;
        this.chartOptions.labels.splice(0, this.chartOptions.labels.length, ...keys);
        // const colors = this.generateColors(keys.length);

        // const colors = ['#84b3ea', '#dd481c', '#1d7a8a', '#3096e9', '#1ea242', '#c1857b', '#71b98e', '#83b', '#ac8a98', '#9c57', '#896a53'];
        // console.log(colors);

        // this.chartOptions.colors.splice(0, this.chartOptions.colors.length, ...colors);

        this.forceChartRerender();
        this.loading = false;

      });
    },

    generateColors(length) {
      const colors = [];
      for (let i = 0; i < length; i++) {
        const color = '#' + Math.floor(Math.random() * 16777215).toString(16);
        colors.push(color);
      }
      return colors;
    }
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
