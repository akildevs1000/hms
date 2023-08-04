<template>
  <div v-if="can('management_summary_access') && can('management_summary_view')">
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="3">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
              class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense class="custom-text-box shadow-none"
              solo flat label="To" :hide-details="true"></v-text-field>
          </template>
          <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col xs="12" sm="12" class="pl-0 ml-0" md="3" cols="12">
        <v-btn color="primary" class="l-0" height="37" @click="generateReport">
          Generate Report
          <!-- <v-icon right dark>mdi mdi-magnify</v-icon> -->
        </v-btn>
      </v-col>
    </v-row>

    <div>
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
          <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
            color="primary"></v-progress-linear>

          <tr v-for="(item, index) in data" :key="index" style="font-size: 12px">
            <td style="width: 2%">
              <b>{{ ++index }}</b>
            </td>
            <td style="width: 20%">{{ item.date || "---" }}</td>
            <td>
              <v-progress-linear :value="item.sold" color="teal" height="20" width="20" rounded>
                <template v-slot:default="{ value }" style="position: relative">
                  <strong :style="{
                    left: value + 1 + '%',
                    // left: (value * 100) / 100 + '%',
                    transform: 'translateX(-50%)',
                    position: 'absolute',
                    top: '0',
                  }">{{ Math.ceil(value) }}%</strong>
                  <strong :style="{
                    // left: ((98 - value) * 100) / 100 + '%',
                    right: 0 + '%',
                    transform: 'translateX(-50%)',
                    position: 'absolute',
                    top: '0',
                  }">{{ Math.ceil(100 - value) }}%</strong>
                </template>
              </v-progress-linear>
            </td>
            <!-- <td style="width: 20%">
              <v-progress-linear :value="item.unsold" color="red" height="20">
                <template v-slot:default="{ value }">
                  <strong>{{ Math.ceil(value) }}%</strong>
                </template>
              </v-progress-linear>
            </td> -->
          </tr>
        </table>
      </v-card>
      <div>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                :total-visible="12"></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>
  </div>
  <NoAccess v-else />
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
      // -------------------end chart ----------------

      Model: "Report",
      endpoint: "get_occupancy_rate",

      from_date: new Date(new Date().getFullYear(), new Date().getMonth(), 2)
        .toISOString()
        .slice(0, 10),
      from_menu: false,
      loading: false,
      to_date: new Date().toJSON().slice(0, 10),
      to_menu: false,

      pagination: {
        current: 1,
        total: 0,
        per_page: 100,
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
          text: "Sold (%)",
        },
        // {
        //   text: "Unsold (%)",
        // },
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
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    viewAgentsBilling(item) {
      this.$router.push(`/agents/details/${item.source}`);
    },

    commonMethod() {
      this.getDataFromApi();
    },

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.record.data;
        this.pagination.current = data.record.current_page;
        this.pagination.total = data.record.last_page;
        this.loading = false;
      });
    },

    generateReport() {
      if (!(this.from_date || this.to_date)) {
        alert("Please select date to generate report");
        return;
      }

      let payload = {
        company_id: this.$auth.user.company.id,
        from: this.from_date,
        to: this.to_date,
      };
      this.$axios
        .post("/generate_occupancy_rate", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.getDataFromApi();
          }
        })
        .catch((e) => console.log(e));
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
