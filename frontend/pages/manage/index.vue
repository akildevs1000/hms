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
          <v-toolbar color="primary" dense flat dark>
            <span>{{ formTitle }} {{ Model }}</span>
          </v-toolbar>
          <v-divider class="py-0 my-0"></v-divider>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col md="2" class="mt-2 pr-0 mr-0">
                  <h5>Weekdays</h5>
                </v-col>
                <v-col md="1" sm="4" class="py-0 my-0 ml-3" v-for="(day, index) in Weekdays" :key="index">
                  <v-checkbox v-model="selectedWeekDays" :label="day.name" :value="day.name"></v-checkbox>
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
                  <v-date-picker v-model="dates" full-width range></v-date-picker>
                  <span v-if="errors && errors.dates" class="error--text">{{
                    errors.dates[0]
                  }}</span>
                </v-col>
                <v-col cols="12" sm="12">
                  <v-textarea rows="3" v-model="description" label="Description" outlined dense
                    :hide-details="true"></v-textarea>
                  <span v-if="errors && errors.description" class="error--text">{{ errors.description[0] }}</span>
                </v-col>
                <v-card-actions>
                  <v-btn class="primary" @click="update_holidays" v-if="isUpdate">Update</v-btn>
                  <v-btn class="primary" @click="store_holidays" v-else>
                    Save
                  </v-btn>
                  <v-btn class="accent" @click="clear">CLear</v-btn>
                </v-card-actions>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-col>

      <v-dialog v-model="priceEditDialog" max-width="600px">
        <v-card>
          <v-card-title> Edit </v-card-title>
          <v-divider></v-divider>
          <h3 style=" text-transform: capitalize; margin: 11px 20px -25px 20px;color: #aaaaaa;">
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
          <h3 style=" text-transform: capitalize; margin: 11px 20px -25px 20px;color: #aaaaaa;">
            {{ editPriceList.name }}
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
            <v-btn color="accent" @click="weekendDialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>


      <v-col md="7" sm="12" class="float-right">
        <v-card>
          <v-toolbar color="primary" dark flat>
            <template v-slot:extension>
              <v-tabs v-model="tab" align-with-title>
                <v-tabs-slider color="yellow"></v-tabs-slider>
                <v-tab v-for="item in items" :key="item">
                  {{ item }}
                </v-tab>
              </v-tabs>
            </template>
          </v-toolbar>
          <v-tabs-items v-model="tab">
            <!-- weekdays -->
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
                      <v-menu bottom left>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list width="120" dense>
                          <v-list-item @click="priceEditItem(item)">
                            <v-list-item-title style="cursor: pointer">
                              <v-icon color="secondary" small>
                                mdi-pencil
                              </v-icon>
                              Edit
                            </v-list-item-title>
                          </v-list-item>
                          <!-- <v-list-item @click="priceDeleteItem(item)">
                            <v-list-item-title style="cursor: pointer">
                              <v-icon color="error" small> mdi-delete </v-icon>
                              Delete
                            </v-list-item-title>
                          </v-list-item> -->
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                </table>
              </v-card>
            </v-tab-item>

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
                      <v-menu bottom left>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list width="120" dense>
                          <v-list-item @click="weekendEditItem(item)">
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
                <table>
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
                </v-row>
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
                      <v-menu bottom left>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn dark-2 icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list width="120" dense>
                          <v-list-item @click="priceEditItem(item)">
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
</template>
<script>
export default {
  data: () => ({
    tab: null,
    items: ["Weekdays", "Weekend", "Holidays", "Prices"],
    Model: "Holidays Price",
    selectedWeekDays: [],
    dates: [],
    description: "",

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
    headers: [
      { text: "#" },
      { text: "From" },
      { text: "To" },
      { text: "Desc" },
      { text: "Action" },
    ],
    editedIndex: -1,
    response: "",
    priceList: [],
    weekendList: [],
    errors: [],
  }),

  created() {
    this.loading = true;
    this.getDataFromApi();
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
    can(permission) {
      let user = this.$auth;
      return;
      return (
        (user && user.permissions.some((e) => e.permission == permission)) ||
        user.master
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

    editItem(item) {
      this.id = item.id;

      this.description = item.description;
      this.isUpdate = true;
      this.dates = [item.from, item.to];
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
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
        },
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

    update_holidays() {
      let payload = {
        dates: this.dates,
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
            console.log(data.status);
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

    store_holidays() {
      console.log(this.dates);
      let payload = {
        dates: this.dates,
        description: this.description,
        company_id: this.$auth.user.company.id,
      };
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
