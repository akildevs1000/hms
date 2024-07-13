<template>
  <div
    v-if="can('settings_room_price_access') && can('settings_room_price_view')"
  >
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <!-- <v-row class="mt-5 mb-0">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
      </v-col>
      <v-col cols="6" class="text--right">
        <v-btn
          dense
          small
          class="primary"
          style="float: right"
          @click="updatePriceToWordpress()"
          >Upload Prices To Wordpress Site</v-btn
        >
      </v-col>
    </v-row> -->

    <v-row>
      <v-dialog v-model="holidayDialog" max-width="40%">
        <v-card>
          <v-toolbar class="blue" dark dense flat>
            <span>{{ formTitle }} {{ Model }}</span>
            <v-spacer></v-spacer>
            <v-icon class="pa-0" @click="holidayDialog = false"
              >mdi-close</v-icon
            >
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col md="12">
                  <v-row>
                    <v-col md="12" cols="12">
                      <v-text-field
                        v-model="description"
                        placeholder="Name"
                        label="Name"
                        outlined
                        :hide-details="true"
                        dense
                      ></v-text-field>
                      <span
                        v-if="errors && errors.description"
                        class="error--text"
                        >{{ errors.description[0] }}</span
                      >
                    </v-col>
                    <v-col md="6" cols="12">
                      <v-menu
                        v-model="from_date_menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="from_date"
                            @change="getDays(from_date)"
                            label="From Date"
                            v-on="on"
                            v-bind="attrs"
                            :hide-details="true"
                            dense
                            outlined
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          no-title
                          v-model="from_date"
                          @input="from_date_menu = false"
                        ></v-date-picker>
                      </v-menu>
                      <span v-if="errors && errors.email" class="error--text"
                        >{{ errors.email[0] }}
                      </span>
                    </v-col>
                    <v-col md="6" cols="12">
                      <v-menu
                        v-model="to_date_menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="to_date"
                            label="To Date"
                            v-on="on"
                            v-bind="attrs"
                            :hide-details="true"
                            dense
                            outlined
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          no-title
                          v-model="to_date"
                          @input="to_date_menu = false"
                        ></v-date-picker>
                      </v-menu>
                      <span v-if="errors && errors.email" class="error--text"
                        >{{ errors.email[0] }}
                      </span>
                    </v-col>
                    <v-col md="12" cols="12">
                      <v-text-field
                        v-model="numberOfDays"
                        placeholder="Number of Days"
                        label="Number of Days"
                        outlined
                        :hide-details="true"
                        dense
                      ></v-text-field>
                      <span
                        v-if="errors && errors.numberOfDays"
                        class="error--text"
                        >{{ errors.numberOfDays[0] }}</span
                      >
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" class="text-right">
                  <v-btn
                    small
                    class="grey white--text"
                    @click="holidayDialog = false"
                  >
                    Cancel
                  </v-btn>
                  <!-- <v-btn small class="primary">Save</v-btn> -->
                  <v-btn
                    small
                    class="primary"
                    @click="update_holidays"
                    v-if="isUpdate"
                    >Update</v-btn
                  >
                  <v-btn small class="primary" @click="store_holidays" v-else>
                    Save
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="priceEditDialog" max-width="600px">
        <v-card>
          <v-toolbar flat dense>
            Edit <v-spacer></v-spacer>
            <v-icon @click="priceEditDialog = false">mdi-close</v-icon>
          </v-toolbar>

          <v-divider></v-divider>
          <h4
            style="
              text-transform: capitalize;
              margin: 11px 20px -25px 20px;
              color: #aaaaaa;
            "
          >
            {{ editPriceList.name }}
          </h4>
          <v-card-text class="mt-8">
            <v-row>
              <v-col md="4">
                <v-text-field
                  v-model="editPriceList.weekday_price"
                  label="Weekdays Amount"
                  placeholder="Weekdays Amount"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
              <v-col md="4">
                <v-text-field
                  v-model="editPriceList.weekend_price"
                  label="	Weekend Amount"
                  placeholder="Weekend Amount"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
              <v-col md="4">
                <v-text-field
                  v-model="editPriceList.holiday_price"
                  label="Holiday Amount"
                  placeholder="Holiday Amount"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
            </v-row>
            <v-row v-if="editPriceList.name == 'Hall'">
              <v-col md="3">
                <v-text-field
                  v-model="editPriceList.projector_charges"
                  label="Projector Charges"
                  placeholder="Projector Charges"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
              <v-col md="3">
                <v-text-field
                  v-model="editPriceList.cleaning_charges"
                  label="Cleaning Charges"
                  placeholder="Cleaning Charges"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
              <v-col md="3">
                <v-text-field
                  v-model="editPriceList.electricity_charges"
                  label="E&B Charges"
                  placeholder="E&B Charges"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
              <v-col md="3">
                <v-text-field
                  v-model="editPriceList.audio_charges"
                  label="Audio Charges"
                  placeholder="Audio Charges"
                  id="id"
                  outlined
                  dense
                >
                </v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="text-right">
                <v-btn
                  color="grey white--text"
                  small
                  @click="priceEditDialog = false"
                >
                  Close
                </v-btn>
                <v-btn class="primary" small @click="update_price">
                  Update
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="weekendDialog" max-width="700px">
        <v-card>
          <v-toolbar flat dense>
            Edit <v-spacer></v-spacer>
            <v-icon @click="weekendDialog = false">mdi-close</v-icon>
          </v-toolbar>
          <v-divider></v-divider>
          <h4
            style="
              text-transform: capitalize;
              margin: 11px 20px -25px 20px;
              color: #aaaaaa;
            "
          >
            <!-- {{ editPriceList }} -->
          </h4>
          <v-card-text class="mt-8">
            <v-row>
              <v-col cols="2" class="pt-5">
                <h4>Weekdays</h4>
              </v-col>
              <v-col cols="10">
                <v-row>
                  <v-col
                    cols="1"
                    class="text-right ml-5"
                    v-for="(day, index) in Weekdays"
                    :key="index"
                  >
                    <v-checkbox
                      hide-details
                      dense
                      v-model="editWeekend.name"
                      :label="day.name"
                      :value="day.name"
                    >
                    </v-checkbox>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="12">
                <v-btn small class="primary" @click="update_weekend">
                  Update
                </v-btn>
                <v-btn
                  small
                  class="grey white--text"
                  @click="weekendDialog = false"
                  >Close</v-btn
                >
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-col md="12" sm="12" class="float-right">
        <v-card>
          <v-tabs v-model="tab">
            <v-spacer></v-spacer>
            <v-tab v-for="item in items" :key="item">
              {{ item }}
            </v-tab>
          </v-tabs>

          <v-tabs-items v-model="tab">
            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3 px-2" elevation="0">
                <v-data-table
                  :headers="headers"
                  :items="priceList"
                  :loading="loading"
                  hide-default-footer
                >
                  <template v-slot:item.index="{ index }">
                    {{ index + 1 }}
                  </template>

                  <template v-slot:item.name="{ item }">
                    <span style="text-transform: uppercase">{{
                      item.name
                    }}</span>
                  </template>

                  <template v-slot:item.action="{ item }">
                    <v-menu bottom left v-if="can('settings_room_price_edit')">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn dark-2 icon v-bind="attrs" v-on="on">
                          <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                      </template>
                      <v-list width="120" dense>
                        <v-list-item
                          v-if="can('settings_room_price_edit')"
                          @click="priceEditItem(item)"
                        >
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="secondary" small>mdi-pencil</v-icon>
                            Edit
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
                <v-data-table
                  :headers="weekendHeaders"
                  :items="weekendList"
                  :loading="loading"
                  hide-default-footer
                >
                  <template v-slot:item.index="{ index }">
                    {{ index + 1 }}
                  </template>

                  <template v-slot:item.day="{ item }">
                    <span v-for="(w, i) in item.day" :key="i">
                      {{ w
                      }}<span v-if="item && item.day.length - 1 !== i">, </span>
                    </span>
                  </template>

                  <template v-slot:item.action="{ item }">
                    <v-menu bottom left v-if="can('settings_room_price_edit')">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn dark-2 icon v-bind="attrs" v-on="on">
                          <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                      </template>
                      <v-list width="120" dense>
                        <v-list-item
                          v-if="can('settings_room_price_edit')"
                          @click="weekendEditItem(item)"
                        >
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="secondary" small>mdi-pencil</v-icon>
                            Edit
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </template>
                </v-data-table>
              </v-card>
            </v-tab-item>
            <!-- weekend -->

            <v-tab-item>
              <v-card class="mb-5 rounded-md mt-3 px-2" elevation="0">
                <div class="d-flex justify-center">
                  <v-btn
                    small
                    class="pt-4 pb-4 mr-1 elevation-0"
                    color="#ECF0F4"
                    @click="toggleFilter"
                  >
                    Search
                    <v-icon right>mdi-magnify</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="can('settings_room_price_create')"
                    @click="holidayDialog = true"
                    small
                    class="pt-4 pb-4 elevation-0"
                    color="#ECF0F4"
                  >
                    New
                    <v-icon right>mdi-plus-circle</v-icon>
                  </v-btn>
                </div>

                <v-data-table
                  :headers="holidayHeaders"
                  :items="data"
                  :options.sync="holidayOptions"
                  :server-items-length="totalUserData"
                  :loading="loading"
                  :footer-props="{
                    itemsPerPageOptions: [50, 100, 500, 1000],
                  }"
                >
                  <!-- <template v-slot:header="{ props: { headers } }"> -->
                  <template v-slot:header="{ props: { headers } }">
                    <tr v-if="isFilter">
                      <th v-for="header in holidayHeaders" :key="header.text">
                        <v-text-field
                          v-if="header.filterable"
                          v-model="filters[header.value]"
                          :label="header.text"
                          clearable
                          @input="applyFilters"
                          dense
                          outlined
                          flat
                          append-icon="mdi-magnify"
                        ></v-text-field>
                      </th>
                    </tr>
                  </template>
                  <template v-slot:item.actions="{ item }">
                    <v-menu
                      bottom
                      left
                      v-if="
                        can('settings_room_price_edit') ||
                        can('settings_room_price_delete')
                      "
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn dark-2 icon v-bind="attrs" v-on="on">
                          <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                      </template>
                      <v-list width="120" dense>
                        <v-list-item
                          v-if="can('settings_room_price_edit')"
                          @click="editItem(item)"
                        >
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="secondary" small>
                              mdi-pencil
                            </v-icon>
                            Edit
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item
                          v-if="can('settings_room_price_delete')"
                          @click="deleteItem(item)"
                        >
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
    items: ["Prices", "Weekend", "Holidays"],
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
      { text: "Name", value: "description", filterable: true },
      { text: "From", value: "from", filterable: false },
      { text: "to", value: "to", filterable: false },
      { text: "Actions", value: "actions", filterable: false, sortable: false },
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
    headers: [
      { text: "#", value: "index", sortable: false },
      { text: "Room Type", value: "name" },
      { text: "Weekdays Amount", value: "weekday_price" },
      { text: "Weekend Amount", value: "weekend_price" },
      { text: "Holiday Amount", value: "holiday_price" },
      { text: "Action", value: "action", sortable: false },
    ],
    weekendHeaders: [
      { text: "#", value: "index", sortable: false },
      { text: "Weekend", value: "day" },
      { text: "Action", value: "action", sortable: false },
    ],
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
      !val ? this.close() : "";
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
        this.getDataFromApi();
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
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
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
      let sortedDesc = sortDesc[0];
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          page: page,
          itemsPerPage: itemsPerPage,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          ...this.filters,
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
        projector_charges: this.editPriceList.projector_charges,
        cleaning_charges: this.editPriceList.cleaning_charges,
        electricity_charges: this.editPriceList.electricity_charges,
        audio_charges: this.editPriceList.audio_charges,
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
            // this.get_price_list();
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
      // this.updatePriceToWordpress();
    },
    updatePriceToWordpress() {
      //console.log(this.$auth.user.company);
      window.open(
        this.$auth.user.company.wordpress_push_prices_url,
        "blank",
        "noreferrer"
      );
      //update price to wordpress
      this.$axios
        .get(this.$auth.user.company.wordpress_push_prices_url, null)
        .then(({ data }) => {
          console.log(data);
        })
        .catch((res) => console.log(res));
    },
    getDays() {
      let f_date = new Date(this.from_date);
      let t_date = new Date(this.to_date);
      let Difference_In_Time = t_date.getTime() - f_date.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      if (days > 0) {
        this.numberOfDays = days;
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
@import url("../../assets/css/tableStyles.css");
</style>
