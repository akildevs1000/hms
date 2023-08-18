<template>
  <div v-if="can('settings_room_price_access') && can('settings_room_price_view')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-0">
      <v-col cols="6">
        <h3>{{ Model }}</h3>

      </v-col>
      <v-col cols="6"> </v-col>
    </v-row>

    <v-row>
      <v-dialog v-model="holidayDialog" max-width="40%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <v-toolbar color="background" dense flat dark>
              <span>{{ formTitle }} {{ Model }}</span>
            </v-toolbar>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="holidayDialog = false">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-container>
            <v-row>
              <v-col md="12" lg="12">
                <v-card elevation="0">
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col md="12">
                          <v-row>
                            <v-col md="12" cols="12">
                              <v-text-field v-model="description" placeholder="Name" label="Name" outlined
                                :hide-details="true" dense></v-text-field>
                              <span v-if="errors && errors.description" class="error--text">{{ errors.description[0]
                              }}</span>
                            </v-col>
                            <v-col md="6" cols="12">
                              <v-menu v-model="from_date_menu" :close-on-content-click="false" :nudge-right="40"
                                transition="scale-transition" offset-y min-width="auto">
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field v-model="from_date" @change="getDays(from_date)" label="From Date"
                                    v-on="on" v-bind="attrs" :hide-details="true" dense outlined></v-text-field>
                                </template>
                                <v-date-picker v-model="from_date" @input="from_date_menu = false"></v-date-picker>
                              </v-menu>
                              <span v-if="errors && errors.email" class="error--text">{{ errors.email[0]
                              }}
                              </span>
                            </v-col>
                            <v-col md="6" cols="12">
                              <v-menu v-model="to_date_menu" :close-on-content-click="false" :nudge-right="40"
                                transition="scale-transition" offset-y min-width="auto">
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field v-model="to_date" label="To Date" v-on="on" v-bind="attrs"
                                    :hide-details="true" dense outlined></v-text-field>
                                </template>
                                <v-date-picker v-model="to_date" @input="to_date_menu = false"></v-date-picker>
                              </v-menu>
                              <span v-if="errors && errors.email" class="error--text">{{ errors.email[0]
                              }}
                              </span>
                            </v-col>
                            <v-col md="12" cols="12">
                              <v-text-field v-model="numberOfDays" placeholder="Number of Days" label="Number of Days"
                                outlined :hide-details="true" dense></v-text-field>
                              <span v-if="errors && errors.numberOfDays" class="error--text">{{ errors.numberOfDays[0]
                              }}</span>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                      <v-card-actions>
                        <v-btn class="error" @click="holidayDialog = false">
                          Cancel
                        </v-btn>
                        <!-- <v-btn class="primary">Save</v-btn> -->
                        <v-btn class="primary" @click="update_holidays" v-if="isUpdate">Update</v-btn>
                        <v-btn class="primary" @click="store_holidays" v-else>
                          Save
                        </v-btn>
                      </v-card-actions>

                    </v-container>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="priceEditDialog" max-width="600px">
        <v-card>
          <v-card-title> Edit </v-card-title>
          <v-divider></v-divider>
          <h3 style="
              text-transform: capitalize;
              margin: 11px 20px -25px 20px;
              color: #aaaaaa;
            ">
            {{ editPriceList.name }}
          </h3>
          <v-card-text class="mt-8">
            <v-row>
              <v-col md="4">
                <v-text-field v-model="editPriceList.weekday_price" label="Weekdays Amount" placeholder="Weekdays Amount"
                  id="id" outlined dense>
                </v-text-field>
              </v-col>
              <v-col md="4">
                <v-text-field v-model="editPriceList.weekend_price" label="	Weekend Amount" placeholder="Weekend Amount"
                  id="id" outlined dense>
                </v-text-field>
              </v-col>
              <v-col md="4">
                <v-text-field v-model="editPriceList.holiday_price" label="Holiday Amount" placeholder="Holiday Amount"
                  id="id" outlined dense>
                </v-text-field>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn class="primary" @click="update_price"> Update </v-btn>
            <v-btn color="accent" @click="priceEditDialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="weekendDialog" max-width="700px">
        <v-card>
          <v-card-title> Edit </v-card-title>
          <v-divider></v-divider>
          <h3 style="
              text-transform: capitalize;
              margin: 11px 20px -25px 20px;
              color: #aaaaaa;
            ">
            <!-- {{ editPriceList }} -->
          </h3>
          <v-card-text class="mt-8">
            <v-row>
              <v-col md="2" class="mt-2 pr-0 mr-0">
                <h5>Weekdays</h5>
              </v-col>
              <v-col md="1" sm="4" class="py-0 my-0 ml-3" v-for="(day, index) in Weekdays" :key="index">
                <v-checkbox v-model="editWeekend.name" :label="day.name" :value="day.name">
                </v-checkbox>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn class="primary" @click="update_weekend"> Update </v-btn>
            <v-btn color="accent" @click="weekendDialog = false"> Close </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-col md="12" sm="12" class="float-right">
        <v-card>
          <v-tabs v-model="tab" align-with-title background-color="background" dark show-arrows>
            <v-spacer></v-spacer>
            <v-tab v-for="item in items" :key="item" active-class="active-link">
              {{ item }}
            </v-tab>
            <v-tabs-slider color="#1259a7"></v-tabs-slider>
          </v-tabs>

          <v-tabs-items v-model="tab">
            <!-- weekdays -->

            <!-- weekdays -->
            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3 px-2" elevation="0">
                <table>
                  <tr style="font-size: 12px">
                    <th>#</th>
                    <th>Weekend</th>
                    <th>Action</th>
                  </tr>
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>
                  <tr style="font-size: 12px" v-for="(item, index) in weekendList" :key="index">
                    <td>{{ ++index }}</td>
                    <td>
                      <span v-for="(w, i) in item.day" :key="i">
                        {{ w }}
                        <span v-if="item && item.day.length - 1 !== i">, </span>
                      </span>
                    </td>
                    <td class="text-left">
                      <v-menu bottom left v-if="can('settings_room_price_edit')">
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list width="120" dense>
                          <v-list-item v-if="can('settings_room_price_edit')" @click="weekendEditItem(item)">
                            <v-list-item-title style="cursor: pointer">
                              <v-icon color="secondary" small>
                                mdi-pencil
                              </v-icon>
                              Edit
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                </table>
              </v-card>
            </v-tab-item>
            <!-- weekend -->

            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3 px-2" elevation="0">
                <div class="d-flex justify-center">
                  <v-btn small class="pt-4 pb-4 mr-1 elevation-0" color="#ECF0F4" @click="toggleFilter">
                    Search
                    <v-icon right>mdi-magnify</v-icon>
                  </v-btn>
                  <v-btn v-if="can('settings_room_price_create')" @click="holidayDialog = true" small
                    class="pt-4 pb-4 elevation-0" color="#ECF0F4">
                    New
                    <v-icon right>mdi-plus-circle</v-icon>
                  </v-btn>
                </div>

                <!-- <table>
                  <tr>
                    <th style="font-size: 12px" v-for="(item, index) in headers" :key="index">
                      <span v-html="item.text"></span>
                    </th>
                  </tr>
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>
                  <tr style="font-size: 12px" v-for="(item, index) in data" :key="index">
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
                    <td class="text-left">
                      <v-menu bottom left>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list width="120" dense>
                          <v-list-item @click="editItem(item)">
                            <v-list-item-title style="cursor: pointer">
                              <v-icon color="secondary" small>
                                mdi-pencil
                              </v-icon>
                              Edit
                            </v-list-item-title>
                          </v-list-item>
                          <v-list-item @click="deleteItem(item)">
                            <v-list-item-title style="cursor: pointer">
                              <v-icon color="error" small> mdi-delete </v-icon>
                              Delete
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                </table>

                <v-row>
                  <v-col md="12" class="float-right">
                    <div class="float-right">
                      <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                        :total-visible="12"></v-pagination>
                    </div>
                  </v-col>
                </v-row> -->

                <v-data-table :headers="holidayHeaders" :items="data" :options.sync="holidayOptions"
                  :server-items-length="totalUserData" :loading="loading" class="elevation-1 mt-2" :footer-props="{
                    itemsPerPageOptions: [50, 100, 500, 1000],
                  }">
                  <!-- <template v-slot:header="{ props: { headers } }"> -->
                  <template v-slot:header="{ props: { headers } }">
                    <tr v-if="isFilter">
                      <th v-for="header in holidayHeaders" :key="header.text">
                        <v-text-field v-if="header.filterable" v-model="filters[header.value]" :label="header.text"
                          clearable @input="applyFilters" dense outlined flat append-icon="mdi-magnify"></v-text-field>
                      </th>
                    </tr>
                  </template>
                  <template v-slot:item.actions="{ item }">

                    <v-menu bottom left v-if="can('settings_room_price_edit') || can('settings_room_price_delete')">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn dark-2 icon v-bind="attrs" v-on="on">
                          <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                      </template>
                      <v-list width="120" dense>
                        <v-list-item v-if="can('settings_room_price_edit')" @click="editItem(item)">
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="secondary" small>
                              mdi-pencil
                            </v-icon>
                            Edit
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="can('settings_room_price_delete')" @click="deleteItem(item)">
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="error" small> mdi-delete </v-icon>
                            Delete
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </template>
                </v-data-table>


              </v-card>
            </v-tab-item>

            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3 px-2" elevation="0">
                <table>
                  <tr style="font-size: 12px">
                    <th>#</th>
                    <th>Room Type</th>
                    <th>Weekdays Amount</th>
                    <th>Weekend Amount</th>
                    <th>Holiday Amount</th>
                    <th>Action</th>
                  </tr>
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>
                  <tr style="font-size: 12px" v-for="(item, index) in priceList" :key="index">
                    <td>{{ ++index }}</td>
                    <td style="text-transform: uppercase">{{ item.name }}</td>
                    <td>{{ item.weekday_price }}</td>
                    <td>{{ item.weekend_price }}</td>
                    <td>{{ item.holiday_price }}</td>
                    <td class="text-left">
                      <v-menu bottom left v-if="can('settings_room_price_edit')">
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list width="120" dense>
                          <v-list-item v-if="can('settings_room_price_edit')" @click="priceEditItem(item)">
                            <v-list-item-title style="cursor: pointer">
                              <v-icon color="secondary" small>
                                mdi-pencil
                              </v-icon>
                              Edit
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                </table>
              </v-card>
            </v-tab-item>

          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    tab: null,
    // items: ["Weekdays", "Weekend", "Holidays", "Prices"],
    items: ["Weekend", "Holidays", "Prices"],
    Model: "Holidays Price",
    selectedWeekDays: [],
    dates: [],
    description: "",
    numberOfDays: "",
    from_date_menu: false,
    from_date: "",
    to_date: "",
    to_date_menu: false,

    holidayOptions: {},
    filters: {},
    isFilter: false,
    totalUserData: 0,
    holidayHeaders: [
      { text: 'Name', value: 'description', filterable: true },
      { text: 'From', value: 'from', filterable: false },
      { text: 'to', value: 'to', filterable: false },
      { text: 'Actions', value: 'actions', filterable: false, sortable: false },
    ],

    Weekdays: [
      { name: "Mon" },
      { name: "Tue" },
      { name: "Wed" },
      { name: "Thu" },
      { name: "Fri" },
      { name: "Sat" },
      { name: "Sun" },
    ],

    pagination: {
      current: 1,
      total: 0,
      per_page: 17,
      status: "-1",
    },
    editPriceList: {
      weekday_price: 0,
      weekend_price: 0,
      holiday_price: 0,
    },

    editWeekend: {
      name: [],
    },

    weekendId: "",

    options: {},
    endpoint: "holiday",
    search: "",
    holidayDialog: false,
    priceEditDialog: false,
    weekendDialog: false,
    isUpdate: false,
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    id: "",
    priceId: "",
    // headers: [
    //   { text: "#" },
    //   { text: "From" },
    //   { text: "To" },
    //   { text: "Desc" },
    //   { text: "Action" },
    // ],
    editedIndex: -1,
    response: "",
    priceList: [],
    weekendList: [],
    errors: [],
  }),

  watch: {

    holidayDialog(val) {
      !val ? this.close() : ''
      console.log(val);
    },

    from_date() {
      this.getDays();
    },

    to_date() {
      this.getDays();
    },

    holidayOptions: {
      handler() {
        this.getDataFromApi()
      },
      deep: true,
    },
  },

  created() {
    this.loading = true;
    this.get_price_list();
    this.get_weekends();
  },

  computed: {
    dateRangeText() {
      return this.dates.join(" ~ ");
    },
    formTitle() {
      return this.isUpdate ? "Update" : "Create";
    },
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

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    onPageChange() {
      this.getDataFromApi();
    },

    toggleFilter() {
      this.isFilter = !this.isFilter;
      console.log(this.isFilter);
    },

    close() {
      this.description = "";
      this.from_date = "";
      this.to_date = "";
      this.numberOfDays = "";
    },

    editItem(item) {
      this.holidayDialog = true;
      // console.log(item);
      this.id = item.id;

      this.description = item.description;
      this.isUpdate = true;
      this.dates = [item.from, item.to];

      this.from_date = item.from;
      this.to_date = item.to;

    },

    priceEditItem(item) {
      this.priceId = item.id;
      this.editPriceList = item;
      this.priceEditDialog = true;
    },

    weekendEditItem(item) {
      this.weekendId = item.id;
      this.editWeekend.name = item.day;
      console.log(this.editWeekend.name);
      this.weekendDialog = true;
    },

    clear() {
      this.isUpdate = false;
      this.description = "";
      this.dates = [];
    },

    deleteItem(item) {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`holiday` + "/" + item.id, payload)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    getDataFromApi(url = this.endpoint) {

      const { sortBy, sortDesc, page, itemsPerPage } = this.holidayOptions;
      let sortedBy = sortBy[0];
      let sortedDesc = sortDesc[0]
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          page: page,
          itemsPerPage: itemsPerPage,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          ...this.filters

        },
      };
      console.log(options);
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.totalUserData = data.data.total;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    get_price_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get("get_price_list", payload).then(({ data }) => {
        this.priceList = data;
      });
    },

    get_weekends() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get("weekend", payload).then(({ data }) => {
        this.loading = false;
        this.weekendList = data;
      });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    applyFilters() {
      this.getDataFromApi();
    },

    update_holidays() {
      let payload = {
        // dates: this.dates,
        dates: [this.from_date, this.to_date],
        description: this.description,
        company_id: this.$auth.user.company.id,
      };

      this.$axios
        .put(`holiday/${this.id}`, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.holidayDialog = false;
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Holiday successfully updated";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
    },

    update_weekend() {
      let payload = {
        day: this.editWeekend.name,
        company_id: this.$auth.user.company.id,
      };
      this.$axios
        .put(`weekend/${this.weekendId}`, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            this.get_weekends();
            this.weekendDialog = false;
            this.snackbar = true;
            this.response = "Weekend successfully updated";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
    },

    update_price() {
      let payload = {
        weekday_price: this.editPriceList.weekday_price,
        weekend_price: this.editPriceList.weekend_price,
        holiday_price: this.editPriceList.holiday_price,
        company_id: this.$auth.user.company.id,
      };
      console.log(payload);
      this.$axios
        .put(`update_room_price/${this.priceId}`, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            this.get_price_list();
            this.priceEditDialog = false;
            this.snackbar = true;
            this.response = "Price successfully updated";
            this.editPriceList = {};
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
    },

    getDays() {
      let f_date = new Date(this.from_date);
      let t_date = new Date(this.to_date);
      let Difference_In_Time = t_date.getTime() - f_date.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      if (days > 0) {
        (this.numberOfDays = days);
      }
    },

    store_holidays() {
      let payload = {
        // dates: this.dates,
        dates: [this.from_date, this.to_date],
        description: this.description,
        company_id: this.$auth.user.company.id,
      };
      this.$axios
        .post(this.endpoint, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            this.getDataFromApi();
            this.holidayDialog = false;
            this.snackbar = true;
            this.response = "Holiday successfully added";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
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
  padding: 7px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
