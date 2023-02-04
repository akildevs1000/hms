<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col cols="6"> </v-col>
    </v-row>

    <v-row>
      <v-col md="5" sm="12" lg="5">
        <v-card elevation="0">
          <v-toolbar color="background" dense flat dark>
            <span>Create {{ Model }}</span>
          </v-toolbar>
          <v-divider class="py-0 my-0"></v-divider>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col md="2" class="mt-2 pr-0 mr-0">
                  <h5>Weekdays</h5>
                </v-col>
                <v-col
                  md="1"
                  sm="4"
                  class="py-0 my-0 ml-3"
                  v-for="(day, index) in Weekdays"
                  :key="index"
                >
                  <v-checkbox
                    v-model="selectedWeekDays"
                    :label="day.name"
                    :value="day.name"
                  ></v-checkbox>
                </v-col>
              </v-row>

              <!-- <v-row>
                <v-col md="2" class="mt-2 pr-0 mr-0">
                  <h5>Weekend</h5>
                </v-col>
                <v-col
                  md="1"
                  sm="2"
                  class="py-0 my-0 ml-3"
                  v-for="(day, index) in Weekdays"
                  :key="index"
                >
                  <v-checkbox
                    v-model="selectedWeekDays"
                    :label="day.name"
                    :value="day.name"
                  ></v-checkbox>
                </v-col>
              </v-row> -->

              <v-row class="mt-2">
                <v-col cols="12" sm="12">
                  <v-date-picker
                    v-model="dates"
                    full-width
                    range
                  ></v-date-picker>
                  <span v-if="errors && errors.dates" class="error--text">{{
                    errors.dates[0]
                  }}</span>
                </v-col>
                <v-col cols="12" sm="12">
                  <v-textarea
                    rows="3"
                    v-model="description"
                    label="Description"
                    outlined
                    dense
                    :hide-details="true"
                  ></v-textarea>
                  <span
                    v-if="errors && errors.description"
                    class="error--text"
                    >{{ errors.description[0] }}</span
                  >
                </v-col>
                <v-card-actions>
                  <v-btn class="primary" @click="store_holidays">Save</v-btn>
                </v-card-actions>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="7" sm="12" class="float-right">
        <v-card>
          <v-toolbar color="cyan" dark flat>
            <v-app-bar-nav-icon></v-app-bar-nav-icon>
            <v-toolbar-title>Manage</v-toolbar-title>
            <template v-slot:extension>
              <v-tabs v-model="tab" align-with-title>
                <v-tabs-slider color="yellow"></v-tabs-slider>
                <v-tab v-for="item in items" :key="item">
                  {{ item }}
                </v-tab>
                <v-tab> </v-tab>
              </v-tabs>
            </template>
          </v-toolbar>
          <v-tabs-items v-model="tab">
            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3" elevation="0">
                <table>
                  <tr>
                    <th
                      style="font-size:12px"
                      v-for="(item, index) in headers"
                      :key="index"
                    >
                      <span v-html="item.text"></span>
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
                    style="font-size:12px"
                    v-for="(item, index) in data"
                    :key="index"
                  >
                    <td>
                      {{
                        (pagination.current - 1) * pagination.per_page +
                          index +
                          1
                      }}
                    </td>
                    <td>{{ item.from }}</td>
                    <td>{{ item.to }}</td>
                    <td>{{ item.description }}</td>
                  </tr>
                </table>

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
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3" elevation="0">
                <table>
                  <tr style="font-size:12px">
                    <th>#</th>
                    <th>Room Type</th>
                    <th>Weekdays Amount</th>
                    <th>Weekend Amount</th>
                    <th>Holiday Amount</th>
                  </tr>
                  <v-progress-linear
                    v-if="loading"
                    :active="loading"
                    :indeterminate="loading"
                    absolute
                    color="primary"
                  ></v-progress-linear>
                  <tr
                    style="font-size:12px"
                    v-for="(item, index) in priceList"
                    :key="index"
                  >
                    <td>{{ ++index }}</td>
                    <td style="text-transform:uppercase;">{{ item.name }}</td>
                    <td>{{ item.weekday_price }}</td>
                    <td>{{ item.weekend_price }}</td>
                    <td>{{ item.holiday_price }}</td>
                  </tr>
                </table>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    tab: null,
    items: ["Holidays", "Prices"],
    Model: "Organization",
    selectedWeekDays: [],
    dates: [],
    description: "",
    Weekdays: [
      { name: "Mon", selected: false },
      { name: "Tue", selected: false },
      { name: "Wed", selected: false },
      { name: "Thu", selected: false },
      { name: "Fri", selected: false },
      { name: "Sat", selected: false },
      { name: "Sun", selected: false }
    ],

    pagination: {
      current: 1,
      total: 0,
      per_page: 17,
      status: "-1"
    },
    options: {},
    endpoint: "holiday",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "#" },
      { text: "From" },
      { text: "To" },
      { text: "Desc" }
    ],
    editedIndex: -1,
    response: "",
    priceList: [],
    errors: [],
    editedItem: {
      item: null,
      amount: null,
      qty: "",
      payment_modes: "",
      voucher: ""
    }
  }),

  created() {
    this.loading = true;
    this.getDataFromApi();
    this.get_price_list();
  },

  computed: {
    dateRangeText() {
      return this.dates.join(" ~ ");
    }
  },

  methods: {
    onPageChange() {
      this.getDataFromApi();
    },
    can(permission) {
      let user = this.$auth;
      return;
      return (
        (user && user.permissions.some(e => e.permission == permission)) ||
        user.master
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    get_price_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get("get_price_list", payload).then(({ data }) => {
        this.priceList = data;
      });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    store_holidays() {
      let payload = {
        dates: this.dates,
        description: this.description,
        company_id: this.$auth.user.company.id
      };
      // console.log(this.dates);
      // return;
      this.$axios
        .post(this.endpoint, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Holiday successfully added";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch(res => console.log(res));
    }
  }
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
  padding: 7px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
