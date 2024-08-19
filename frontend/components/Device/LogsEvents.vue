<template>
  <div v-if="can('devices_permissions_access') && can('devices_view')">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top="top"
        :color="snackbarColor"
        elevation="24"
      >
        {{ snackbarResponse }}
      </v-snackbar>
    </div>

    <v-row class="pt-2">
      <v-col cols="7"></v-col>

      <v-col cols="3">
        <Calender2
          style="float: right"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
          :defaultFilterType="1"
          :height="'40px '"
        />
      </v-col>
      <v-col cols="2">
        <v-autocomplete
          v-model="device_id"
          :items="[{ serial_number: `All Rooms` }, ...devices_list]"
          item-text="serial_number"
          item-value="serial_number"
          placeholder="Select Room"
          label="Room"
          outlined
          :hide-details="true"
          dense
          @change="getDataFromApi()"
        >
        </v-autocomplete>
      </v-col>
    </v-row>
    <v-card class="mb-5" elevation="0">
      <v-toolbar v-if="viewType == 'page'" class="rounded-md mb-2" dense flat>
        <v-toolbar-title><span>Lights On and Off Logs</span></v-toolbar-title>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              v-bind="attrs"
              v-on="on"
            >
              <v-icon class="ml-2" @click="reload()" dark
                >mdi mdi-reload</v-icon
              >
            </v-btn>
          </template>
          <span>Reload</span>
        </v-tooltip>
        <v-spacer></v-spacer>
      </v-toolbar>
      <v-row>
        <v-col cols="12">
          <v-data-table
            dense
            :headers="headers_table"
            :items="data"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 20, 50, 100, 500, 1000],
            }"
            :server-items-length="totalTableRowsCount"
          >
            <template v-slot:item.sno="{ item, index }">
              {{
                currentPage
                  ? (currentPage - 1) * perPage +
                    (cumulativeIndex + itemIndex(item))
                  : ""
              }}
            </template>
            <template v-slot:item.room.room_no="{ item }">
              {{ item.room.room_no }}</template
            >

            <template v-slot:item.device.room.room_type.name="{ item }">
              {{ caps(item.device.room.room_type.name) }}
            </template>
            <template v-slot:item.duration_minutes="{ item }">
              {{ $dateFormat.minutesToHHMM(item.duration_minutes) }}
            </template>
            <template v-slot:item.status="{ item }">
              <div
                style="color: red"
                v-if="
                  item.booking_status_id == 0 || item.booking_status_id == 4
                "
              >
                Availalbe
              </div>
              <div v-else-if="item.booking_status_id == 1">Booked</div>
              <div v-else-if="item.booking_status_id == 2">Check In</div>
              <div v-else-if="item.booking_status_id == 3">Check Out</div>
              <div v-else style="text-align: center">---</div>
            </template>
            <!-- <template v-slot:item.status="{ item }">
              <v-icon v-if="item.status == 0" color="black"
                >mdi-lightbulb-outline
              </v-icon>
              <v-icon v-else-if="item.status == 1" color="green"
                >mdi-lightbulb-on
              </v-icon>
            </template> -->
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  props: {
    viewType: {
      default: "page",
    },
  },
  data: () => ({
    devices_list: [],
    //datatable varables
    page: 1,
    perPage: 0,
    currentPage: 1,
    cumulativeIndex: 1,
    totalTableRowsCount: 0,
    options: {},
    filters: {},
    isFilter: false,
    data: [],
    loading: false,
    headers_table: [
      {
        text: "#",
        value: "sno",
        align: "left",
      },
      {
        text: "Room Number",
        value: "device.name",
        align: "left",
      },
      {
        text: "Category",
        value: "device.room.room_type.name",
        align: "left",
      },

      {
        text: "ON",
        value: "start_datetime",
        key: "start_datetime",
        align: "left",
      },
      {
        text: "OFF",
        value: "end_datetime",
        key: "end_datetime",
        align: "left",
      },
      {
        text: "Hours(HH:MM)",
        value: "duration_minutes",
        key: "duration_minutes",
        align: "left",
      },
      {
        text: "Room Status",
        value: "status",
        key: "status",
        align: "left",
      },
    ],
    roomList: [],

    endpoint: "devices_logs",

    newItemDialog: false,

    //add edit item details
    editedItem: {},
    editedItemIndex: -1,
    roomTypesForSelectOptions: [],
    errors: {},
    snackbar: false,
    snackbarColor: "red",
    snackbarResponse: "",
    viewMode: false,
    floors: [
      1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
    ],
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    //console.log("device_id", this.$store.state.devices_logs_id);

    this.device_id = this.$store.state.devices_logs_id;
    this.getDataFromApi();
    //this.getroomList();
    this.getDevicesList();
    // Get today's date
    let today = new Date();

    // Subtract 7 days from today
    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 2);

    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    // this.display_title =
    //   "Attendance : " + this.date_from + " to " + this.date_to;
  },
  methods: {
    caps(str) {
      return str.replace(/\b\w/g, (c) => c.toUpperCase());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      this.getDataFromApi();
    },
    AddNewRoom() {
      this.errors = {};
      this.editedItem = {};
      this.editedItemIndex = -1;
      this.viewMode = false;
      this.newItemDialog = true;
    },
    // updateIndex(page) {

    //     this.currentPage = page;
    //     this.cumulativeIndex = (page - 1) * this.perPage;

    // },
    applyFilters() {
      this.$set(this.options, "page", 1);
      this.getDataFromApi(this.endpoint, 1);
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    itemIndex(item) {
      return this.data.indexOf(item);
    },
    reload() {
      this.isFilter = false;
      this.filters = {};
      this.$set(this.options, "page", 1);
      this.getDataFromApi(this.endpoint, 1);
    },
    getDataFromApi(url = this.endpoint, customPage = 0) {
      this.loading = true;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      if (customPage == 1) page = 1;
      this.currentPage = page;
      this.perPage = itemsPerPage;
      if (this.device_id == "All Rooms") {
        this.device_id = null;
      }
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          serial_number: this.device_id,
          from_date: this.date_from,
          to_date: this.date_to,
        },
      };
      this.$axios.get(`devices_logs?page=${page}`, options).then(({ data }) => {
        this.loading = false;
        this.data = data.data;
        this.totalTableRowsCount = data.total;
      });
    },

    getroomList() {
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.$axios.get(`room_list`, options).then(({ data }) => {
        this.roomList = data;
        //this.roomTypesForSelectOptions.unshift({ id: '', name: "All" });
      });
    },

    getDevicesList() {
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.$axios.get(`devices_list`, options).then(({ data }) => {
        this.devices_list = data;
        //this.roomTypesForSelectOptions.unshift({ id: '', name: "All" });
      });
    },
    viewItem(item) {
      this.editItem(item, true);
    },
    editItem(item, viewMode = false) {
      this.errors = {};
      this.viewMode = viewMode;
      this.editedItem = {};
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.newItemDialog = true;

      this.$axios
        .get(`${this.endpoint}/${item.id}`, options)
        .then(({ data }) => {
          this.editedItem = data;
          this.editedItemIndex = item.id;
        });
    },
    save() {
      if (this.editedItemIndex != -1) {
        let options = {
          params: {
            company_id: this.$auth.user.company.id,
            serial_number: this.editedItem.serial_number,
            room_id: this.editedItem.room_id,
            name: this.editedItem.name,
            user_id: this.$auth.user.id,
          },
        };

        this.$axios
          .put(`${this.endpoint}/${this.editedItemIndex}`, options.params)
          .then(({ data }) => {
            if (data.status) {
              this.getDataFromApi();
              this.errors = {};
              this.editedItem = {};
              this.snackbar = true;
              this.snackbarColor = "greeen";
              this.snackbarResponse = data.message;

              this.newItemDialog = false;
            } else {
              if (data.errors) {
                this.errors = data.errors;
              } else {
                this.errors = {};

                this.snackbar = true;
                this.snackbarColor = "red";
                this.snackbarResponse = data.message;
              }
            }
          });
      } else {
        let options = {
          params: {
            company_id: this.$auth.user.company.id,
            user_id: this.$auth.user.id,
            ...this.editedItem,
          },
        };

        this.$axios
          .post(`${this.endpoint}`, options.params)
          .then(({ data }) => {
            if (data.status) {
              this.getDataFromApi();
              this.errors = {};
              this.editedItem = {};
              this.snackbar = true;
              this.snackbarColor = "greeen";
              this.snackbarResponse = data.message;

              this.newItemDialog = false;
            } else {
              if (data.errors) {
                this.errors = data.errors;
              } else {
                this.errors = {};

                this.snackbar = true;
                this.snackbarColor = "red";
                this.snackbarResponse = data.message;
              }
            }
          });
      }
    },
    deleteItem(item) {
      confirm("Are you sure you wish to delete?") &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = data.status;
            this.snackbarResponse = data.message;
          })
          .catch((err) => console.log(err));
    },
  },
};
</script>
