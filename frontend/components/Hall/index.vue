<template>
  <div v-if="can('settings_rooms_access') && can('settings_rooms_view')">
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

    <v-dialog v-model="dialogQRcode" width="600px">
      <v-card>
        <v-card-title dense class="primary white--text background">
          <span> QR Code </span>

          <v-spacer></v-spacer>
          <v-icon @click="dialogQRcode = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <img :src="qrCodeImage" style="width: 500px; height: auto"
          /></v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="newItemDialog" max-width="500">
      <v-card>
        <v-toolbar dense class="primary white--text" flat>
          <span v-if="viewMode">View Room Info </span>
          <span v-else-if="editedItemIndex == -1">Add Room </span>
          <span v-else>Edit Room Info </span>
          <v-spacer></v-spacer>
          <v-icon @click="newItemDialog = false" outlined dark color="white">
            mdi-close
          </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-container class="mt-5">
            <v-row>
              <v-col md="12" cols="12">
                <v-select
                  label="Floor No"
                  v-model="editedItem.floor_no"
                  :disabled="viewMode"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  :items="floors"
                  placeholder="Select Floor"
                ></v-select>
                <span v-if="errors && errors.floor_no" class="error--text">{{
                  errors.floor_no[0]
                }}</span>
              </v-col>
              <v-col md="6" cols="12">
                <v-text-field
                  label="Room No"
                  :disabled="viewMode"
                  v-model="editedItem.room_no"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  placeholder="Room No"
                ></v-text-field>
                <span
                  dense
                  v-if="errors && errors.room_no"
                  class="error--text"
                  >{{ errors.room_no[0] }}</span
                >
              </v-col>
              <v-col md="6" cols="12">
                <v-select
                  label="Category"
                  :disabled="viewMode"
                  :items="roomTypesData"
                  v-model="editedItem.room_type_id"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  item-text="name"
                  item-value="id"
                  placeholder="Select Category"
                >
                </v-select>
                <span
                  v-if="errors && errors.room_type_id"
                  class="error--text"
                  >{{ errors.room_type_id[0] }}</span
                >
              </v-col>
              <v-col md="12" cols="12">
                <v-select
                  label="Available Online Booking?"
                  :disabled="viewMode"
                  selected="0"
                  :items="[
                    {
                      id: 1,
                      name: 'Yes',
                    },
                    { id: 0, name: 'No' },
                  ]"
                  v-model="editedItem.online_available"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  item-text="name"
                  item-value="id"
                  placeholder="Select status"
                >
                </v-select>
                <span v-if="errors && errors.status" class="error--text">{{
                  errors.status[0]
                }}</span>
              </v-col>
              <v-col md="12" cols="12">
                <label> </label>
                <v-select
                label="Status"
                  :disabled="viewMode"
                  selected="0"
                  :items="[
                    { id: '0', name: 'Active' },
                    {
                      id: '1',
                      name: 'In-Active',
                    },
                  ]"
                  v-model="editedItem.status"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  item-text="name"
                  item-value="id"
                  placeholder="Select status"
                >
                </v-select>
                <span v-if="errors && errors.status" class="error--text">{{
                  errors.status[0]
                }}</span>
              </v-col>
              <v-col cols="12" class="text-right">
                <v-btn small @click="newItemDialog = false" dark filled color="grey white--text"
                >Cancel</v-btn
              >
              <v-btn small @click="save()" dark filled color="primary">Save</v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card class="mb-5" elevation="0">
      <v-toolbar dense flat>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn dense text class="ma-0 px-0" small v-bind="attrs" v-on="on">
              Rooms <v-icon class="ml-2" @click="reload()">mdi-reload</v-icon>
            </v-btn>
          </template>
          <span>Reload</span>
        </v-tooltip>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              x-small
              :ripple="false"
              text
              v-bind="attrs"
              v-on="on"
              @click="toggleFilter"
            >
              <v-icon white color="#FFF">mdi-filter</v-icon>
            </v-btn>
          </template>
          <span>Filter</span>
        </v-tooltip>
        <v-spacer></v-spacer>
        <v-tooltip v-if="can('settings_rooms_create')" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              dark
              color="blue"
              small
              v-bind="attrs"
              v-on="on"
              @click="AddNewRoom()"
            >
              <v-icon dark small>mdi-plus</v-icon> Room
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
              itemsPerPageOptions: [50, 100, 500, 1000],
            }"
            class="elevation-1"
            :server-items-length="totalTableRowsCount"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-text-field
                    v-if="header.filterable && !header.filterSpecial"
                    clearable
                    :hide-details="true"
                    v-model="filters[header.value]"
                    no-title
                    outlined
                    dense
                    small
                    :id="header.value"
                    autocomplete="off"
                    @input="applyFilters()"
                  ></v-text-field>
                  <v-autocomplete
                    outlined
                    dense
                    v-model="filters[header.value]"
                    v-if="
                      header.filterable &&
                      header.filterSpecial &&
                      header.key == 'device_latest_status'
                    "
                    :items="[
                      { value: '', title: 'All' },
                      { value: 1, title: 'Occupied' },
                      { value: 0, title: 'Not-occupied' },
                    ]"
                    item-value="value"
                    item-text="title"
                    :hide-details="true"
                    clearable
                    @click:clear="
                      filters[header.key] = '';
                      applyFilters();
                    "
                    @change="applyFilters()"
                  ></v-autocomplete>
                  <v-autocomplete
                    outlined
                    dense
                    v-model="filters[header.value]"
                    v-if="
                      header.filterable &&
                      header.filterSpecial &&
                      header.key == 'status'
                    "
                    :items="[
                      { value: '', title: 'All' },
                      { value: 0, title: 'Active' },
                      { value: 1, title: 'In-Active' },
                    ]"
                    item-value="value"
                    item-text="title"
                    :hide-details="true"
                    clearable
                    @click:clear="
                      filters[header.key] = '';
                      applyFilters();
                    "
                    @change="applyFilters()"
                  ></v-autocomplete>

                  <v-autocomplete
                    v-model="filters[header.key]"
                    v-if="
                      header.filterable &&
                      header.filterSpecial &&
                      header.key == 'room_type'
                    "
                    @change="applyFilters()"
                    clearable
                    @click:clear="
                      filters[header.key] = '';
                      applyFilters();
                    "
                    outlined
                    dense
                    :hide-details="true"
                    :items="roomTypesForSelectOptions"
                    item-text="name"
                    item-value="id"
                  >
                  </v-autocomplete>
                  <v-select
                    v-model="filters[header.key]"
                    v-if="
                      header.filterable &&
                      header.filterSpecial &&
                      header.key == 'floor_no'
                    "
                    :items="floors"
                    outlined
                    dense
                    clearable
                    @click:clear="
                      filters[header.kye] = '';
                      applyFilters();
                    "
                    :hide-details="true"
                    @change="applyFilters()"
                  ></v-select>
                </td>
              </tr>
            </template>
            <template v-slot:item.sno="{ item, index }">
              {{
                currentPage
                  ? (currentPage - 1) * perPage +
                    (cumulativeIndex + itemIndex(item))
                  : ""
              }}
            </template>
            <template v-slot:item.floor_no="{ item }">
              {{ item.floor_no }}</template
            >
            <template v-slot:item.room_type.name="{ item }">
              {{ item.room_type.name }}</template
            >
            <template v-slot:item.latest_status="{ item }">
              <!-- <v-icon
                v-if="item.device && item.device.latest_status == 0"
                color="black"
                >mdi-lightbulb
              </v-icon> -->
              <v-icon
                v-if="item.device && item.device.latest_status == 1"
                color="green"
                >mdi-lightbulb-on-90
              </v-icon>
            </template>
            <template v-slot:item.latest_status_time="{ item }">
              {{ item.device && item.device.latest_status_time }}
            </template>
            <template v-slot:item.status="{ item }">
              {{ item.status == "0" ? "Active" : "In-active" }}
            </template>
            <template v-slot:item.qrcode="{ item }">
              <!-- <v-img
                @click="viewQRCode()"
                :src="item.qrImage"
                style="width: 100px; height: auto"
              /> -->
              <!-- {{ updateQRCodeItem(item) }} -->
              <v-img
                @click="viewQRCode(item)"
                :src="item.qrImage"
                style="width: 100px; height: auto"
              />
              <a style="padding-left: 20px" :href="item.qrURL" target="_blank"
                >Link</a
              >
            </template>
            <template v-slot:item.online_available="{ item }">
              <!-- <v-icon v-if="item.online_available == 0" color="red"
                >mdi mdi-cloud-off
              </v-icon>
              <v-icon v-else color="green">mdi-cloud-check-variant </v-icon> -->

              <v-switch
                v-model="item.online_available"
                color="green"
                @change="changeRoomCloudStatus(item, $event)"
              ></v-switch>
            </template>
            <template
              v-slot:item.options="{ item }"
              v-if="
                can('settings_rooms_view') ||
                can('settings_rooms_edit') ||
                can('settings_rooms_delete')
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
                    v-if="can('settings_rooms_view')"
                    @click="editItem(item, true)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small> mdi-eye </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('settings_rooms_edit')"
                    @click="editItem(item, false)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('settings_rooms_delete')"
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
export default {
  data: () => ({
    dialogQRcode: false,
    qrCodeImage: "",
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
        sortable: false,
        filterable: false,
      },
      {
        text: "Room No",
        value: "room_no",
        align: "left",
        sortable: true,
        filterable: true,
      },
      {
        text: "Floor No",
        value: "floor_no",
        align: "left",
        sortable: true,
        key: "floor_no",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Room Type id",
        value: "room_type_id",
        key: "room_type",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Room Type",
        value: "room_type.name",
        key: "room_type",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Occupied",
        value: "latest_status",
        key: "device_latest_status",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Time",
        value: "latest_status_time",
        key: "latest_status_time",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Online Booking?",
        value: "online_available",
        align: "left",
        key: "online_available",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Room Status",
        value: "status",
        align: "left",
        key: "status",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "QR Code",
        value: "qrcode",
        align: "left",
        key: "qrcode",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },

      { text: "Options", value: "options", align: "left", sortable: false },
    ],
    roomTypesData: [],

    endpoint: "room",

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
    this.getDataFromApi();
    this.getRoomTypesData();
  },
  methods: {
    updateQRCode() {
      this.data.forEach(async (element) => {
        let url =
          process.env.APP_URL +
          "qrcode/" +
          this.$auth.user.company.id +
          "-" +
          element.room_no +
          "-" +
          element.id;
        element.qrURL = url;
        element.qrImage = await this.$qrcode.generate(url, {
          width: 100,
        });
      });
      //let person = this.data.find((e) => e.id == item.id);
    },

    // async updateQRCodeItem(item) {
    //   let person = this.data.find(async (e) => e.id == item.id);
    //   person.qrImage = await this.$qrcode.generate(url, {
    //     width: 100,
    //   });
    // },

    async viewQRCode(item) {
      this.dialogQRcode = true;
      // let url = process.env.APP_URL;

      let url =
        process.env.APP_URL +
        "qrcode/" +
        this.$auth.user.company.id +
        "-" +
        item.room_no +
        "-" +
        item.id;

      await this.generateQRCode(url, 500);
    },

    async generateQRCode(url, width) {
      try {
        console.log(url);
        this.qrCodeImage = await this.$qrcode.generate(url, {
          width: width,
        });
        console.log(this.qrCodeImage);
        if (this.qrCodeImage) return this.qrCodeImage;
      } catch (error) {
        console.error("Error generating QR code:", error);
      }
    },
    // async viewQRCode(url, width) {
    //   try {
    //     this.dialogQRcode = true;
    //     let url = process.env.APP_URL;
    //     console.log(url);
    //     this.qrCodeImage = await this.$qrcode.generate(url, {
    //       width: "500",
    //     });
    //     console.log(this.qrCodeImage);
    //   } catch (error) {
    //     console.error("Error generating QR code:", error);
    //   }
    // },

    changeRoomCloudStatus(item, status) {
      //console.log(roomId, status);
      this.editedItemIndex = item.id;
      this.editedItem = item;
      this.editedItem.online_available = status ? 1 : 0;
      this.save();
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    AddNewRoom() {
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
          type:"hall",
          company_id: this.$auth.user.company.id,
          ...this.filters,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.updateQRCode();
        this.totalTableRowsCount = data.total;
        setTimeout(() => {
          this.loading = false;
        }, 1000);
      });
    },

    getRoomTypesData() {
      let options = { params: { company_id: this.$auth.user.company.id,type:"hall" } };
      this.$axios.get(`get_room_type_list`, options).then(({ data }) => {
        this.roomTypesForSelectOptions = data.data;
        this.roomTypesData = data.data;
        //this.roomTypesForSelectOptions.unshift({ id: '', name: "All" });
      });
    },
    viewItem(item) {
      this.editItem(item, true);
    },
    editItem(item, viewMode = false) {
      this.viewMode = viewMode;
      this.editedItem = {};
      let options = { params: { company_id: this.$auth.user.company.id,type:"hall" } };
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
            room_no: this.editedItem.room_no,
            room_type_id: this.editedItem.room_type_id,
            status: this.editedItem.status,
            user_id: this.$auth.user.id,
            floor_no: this.editedItem.floor_no,
            online_available: this.editedItem.online_available,
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
