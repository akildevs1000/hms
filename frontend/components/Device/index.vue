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
    <v-dialog v-model="DeviceLogDialog" max-width="900">
      <v-card>
        <v-toolbar color="blue" dark dense flat>
          <v-toolbar-title><span>Devices Logs</span></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-icon @click="DeviceLogDialog = false">mdi-close</v-icon>
        </v-toolbar>
        <v-card-text>
          <DeviceLogs viewType="dialog" :key="DeviceLogCompKey" />
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="newItemDialog" max-width="500">
      <v-card>
        <v-toolbar flat dense class="primary white--text">
          <span v-if="viewMode">View Device Info </span>
          <span v-else-if="editedItemIndex == -1">Add Device </span>
          <span v-else>Edit Device Info </span>
          <v-spacer></v-spacer>
          <v-icon @click="newItemDialog = false" outlined dark color="white">
            mdi-close
          </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-container class="mt-4">
            <v-row>
              <v-col cols="6">
                <v-text-field
                  :disabled="viewMode"
                  v-model="editedItem.serial_number"
                  outlined
                  dense
                  small
                  hide-details
                  label="Serial Number"
                ></v-text-field>
                <span
                  dense
                  v-if="errors && errors.serial_number"
                  class="error--text"
                  >{{ errors.serial_number[0] }}</span
                >
              </v-col>
              <v-col cols="6">
                <v-text-field
                  :disabled="viewMode"
                  v-model="editedItem.name"
                  outlined
                  dense
                  small
                  hide-details
                  label="Device Type Name"
                ></v-text-field>
                <span v-if="errors && errors.name" class="error--text">{{
                  errors.name[0]
                }}</span>
              </v-col>
              <v-col cols="6">
                {{ editedItem.utc_time_zone }}
                <v-autocomplete
                  class="pb-0"
                  :hide-details="!editedItem.utc_time_zone"
                  v-model="editedItem.utc_time_zone"
                  placeholder="Time Zone"
                  outlined
                  dense
                  label="Time Zone(Ex:UTC+) *"
                  :items="getTimezones()"
                  item-value="key"
                  item-text="text"
                ></v-autocomplete
              ></v-col>
              <v-col cols="6">
                <v-select
                  :disabled="viewMode"
                  :items="roomList"
                  v-model="editedItem.room_id"
                  outlined
                  dense
                  small
                  hide-details
                  item-text="room_no"
                  item-value="table_id"
                  label="Assign Room"
                >
                </v-select>
                <span v-if="errors && errors.room_id" class="error--text">{{
                  errors.room_id[0]
                }}</span>
              </v-col>

              <v-col cols="12" v-if="!viewMode" class="text-right">
                <v-btn
                  small
                  @click="newItemDialog = false"
                  dark
                  filled
                  color="grey"
                  >Cancel</v-btn
                >
                <v-btn small @click="save()" dark filled color="primary"
                  >Save</v-btn
                >
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card class="mb-5" elevation="0">
      <v-toolbar dense flat>
        <v-toolbar-title><span>Devices</span></v-toolbar-title>
        <v-tooltip top>
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
              <v-icon @click="reload()">mdi mdi-reload</v-icon>
            </v-btn>
          </template>
          <span>Reload</span>
        </v-tooltip>
        <v-spacer></v-spacer>
        <v-tooltip v-if="can('devices_create')" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              small
              color="blue"
              class="white--text"
              dark
              v-bind="attrs"
              v-on="on"
              @click="AddNewRoom()"
            >
              <v-icon small>mdi-plus</v-icon> Device
            </v-btn>
          </template>
          <span>Add New Room</span>
        </v-tooltip>
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
            <template v-slot:item.room_type.name="{ item }">
              {{ item.room_type.name }}</template
            >
            <template v-slot:item.latest_status="{ item }">
              <v-icon v-if="item.latest_status == 0" color="black"
                >mdi-lightbulb-outline
              </v-icon>
              <v-icon v-else-if="item.latest_status == 1" color="green"
                >mdi-lightbulb-on
              </v-icon>
            </template>
            <template v-slot:item.latest_status_time="{ item }">
              {{ item.latest_status_time }}
            </template>
            <template
              v-slot:item.options="{ item }"
              v-if="
                can('devices_view') ||
                can('devices_edit') ||
                can('devices_delete')
              "
            >
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item
                    v-if="can('devices_view')"
                    @click="editItem(item, true)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small> mdi-eye </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('devices_view')"
                    @click="viewStatusLogs(item)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="warning" small>
                        mdi-format-list-checkbox
                      </v-icon>
                      View Logs
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('devices_edit')"
                    @click="editItem(item, false)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('devices_delete')"
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
        </v-col>
      </v-row>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
import timeZones from "../../defaults/utc_time_zones.json";
export default {
  props: ["addNew"],
  data: () => ({
    //datatable varables
    page: 1,
    timeZones: timeZones,
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
        sortable: false,
        filterable: false,
      },
      {
        text: "Serial Number",
        value: "serial_number",
        align: "left",
        sortable: true,
        filterable: true,
      },
      {
        text: "Device Name",
        value: "name",
        key: "name",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Roo No",
        value: "room.room_no",
        align: "left",
        sortable: true,
        key: "room_id",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Timezone",
        value: "utc_time_zone",
        align: "left",
        sortable: true,
        key: "utc_time_zone",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Light Status",
        value: "latest_status",
        align: "left",
        sortable: true,
        key: "room_id",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Light Status Time",
        value: "latest_status_time",
        align: "left",
        sortable: true,
        key: "room_id",
        filterable: true,
        filterSpecial: true,
      },

      { text: "Options", value: "options", align: "left", sortable: false },
    ],
    roomList: [],

    endpoint: "devices",

    newItemDialog: false,
    DeviceLogDialog: false,
    DeviceLogCompKey: 1,
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
    this.getDataFromApi();
    this.getroomList();

    // setInterval(() => {
    //   this.getDataFromApi();
    // }, 1000 * 60 * 2);
  },
  methods: {
    getTimezones() {
      return Object.keys(this.timeZones).map((key) => ({
        offset: this.timeZones[key].offset,
        time_zone: this.timeZones[key].time_zone,
        key: key,
        text: key + " - " + this.timeZones[key].offset,
      }));
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
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

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          ...this.filters,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
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
    viewItem(item) {
      this.editItem(item, true);
    },
    viewStatusLogs(item) {
      this.$store.commit("devices_logs_id", item.serial_number);
      this.DeviceLogDialog = true;
      this.DeviceLogCompKey++;
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
            utc_time_zone: this.editedItem.utc_time_zone,
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
