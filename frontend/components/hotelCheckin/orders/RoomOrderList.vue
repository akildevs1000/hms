<template>
  <div
    v-if="
      can('settings_menu_categories_access') &&
      can('settings_menu_categories_view')
    "
  >
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
    <v-dialog v-model="cancelItemDialog" max-width="30%">
      <v-card>
        <v-card-title dense class="primary white--text background">
          <span>Item Cancellation </span>

          <v-spacer></v-spacer>
          <v-icon @click="cancelItemDialog = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <h4>Cancelation Reason</h4>
            </v-row>
            <v-row>
              <v-textarea outlined v-model="reason_cancelled"> </v-textarea>
            </v-row>

            <v-card-actions class="mt-5" v-if="!viewMode">
              <v-btn @click="cancelItemDialog = false" dark filled color="red"
                >Close</v-btn
              >
              <v-spacer></v-spacer>
              <v-btn @click="confirmCancel()" dark filled color="primary"
                >Confirm to Cancel</v-btn
              >
            </v-card-actions>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
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
          <template v-slot:item.room_id="{ item }">
            {{ item.room.room_no }}</template
          >
          <template v-slot:item.food.name="{ item }">
            {{ item.food.name }}
            <div class="secondary_value">
              {{ item.food.category.name }}
            </div>
          </template>

          <template v-slot:item.food.is_non_veg="{ item }">
            <v-icon :color="item.food.is_non_veg == 1 ? 'red' : 'green'"
              >mdi mdi-square-circle</v-icon
            >
          </template>
          <template v-slot:item.qty="{ item }"> {{ item.qty }}</template>
          <template v-slot:item.request_datetime="{ item }">
            {{ displayTimeFormate(item.request_datetime.split(" ")[1]) }}
            <div class="secondary_value">
              {{ item.request_datetime.split(" ")[0] }}
            </div></template
          >
          <template v-slot:item.status="{ item }">
            <div v-if="item.status == 0">New</div>
            <div v-else-if="item.status == 1" style="color: #85852c">
              Preparing
            </div>
            <div v-else-if="item.status == 2" style="color: green">
              Completed
            </div>
            <div v-else-if="item.status == 3" style="color: red">Cancelled</div>
          </template>
          <template v-slot:item.action1="{ item }">
            <v-btn
              title="Click to Change Status"
              small
              dense
              v-if="item.status == 0"
              color="primary"
              class="ma-2 white--text"
              @click="changeStatusToPreparing(item)"
            >
              Change to Preparing<v-icon right dark>
                mdi mdi-chef-hat
              </v-icon></v-btn
            >
            <v-btn
              title="Click to Change Status"
              small
              dense
              v-else-if="item.status == 1"
              color="primary"
              class="ma-2 white--text"
              @click="changeStatusToDelivered(item)"
            >
              Change to Ready<v-icon right dark>
                mdi mdi-noodles
              </v-icon></v-btn
            >
          </template>
          <template v-slot:item.delivery_datetime="{ item }">
            <div v-if="item.delivery_datetime">
              {{ displayTimeFormate(item.delivery_datetime.split(" ")[1]) }}
              <div class="secondary_value">
                {{ item.delivery_datetime.split(" ")[0] }}
              </div>
            </div>
          </template>
          <template v-slot:item.cancelled_datetime="{ item }">
            <div v-if="item.cancelled_datetime">
              {{ displayTimeFormate(item.cancelled_datetime.split(" ")[1]) }}
              <div class="secondary_value">
                {{ item.cancelled_datetime.split(" ")[0] }}
              </div>

              <p class="secondary_value">{{ item.reason_cancelled }}</p>
            </div>
          </template>
          <template v-slot:item.action2="{ item }">
            <v-btn
              title="View Room All Orders"
              small
              dense
              color="primary"
              class="ma-2 white--text"
              @click="viewRoomItems(item.room_id)"
            >
              View All<v-icon right dark> mdi mdi-room-service </v-icon></v-btn
            >
          </template>
          <template
            v-slot:item.options="{ item }"
            v-if="
              can('settings_menu_categories_view') ||
              can('settings_menu_categories_edit') ||
              can('settings_menu_categories_delete')
            "
          >
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn dark-2 icon v-bind="attrs" v-on="on">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list width="120" dense>
                <v-list-item v-if="item.status != 3" @click="cancelItem(item)">
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="primary" small> mdi-eye </v-icon>
                    Cancel Item
                  </v-list-item-title>
                </v-list-item>
                <!-- <v-list-item
                      v-if="can('settings_menu_categories_view')"
                      @click="editItem(item, true)"
                    >
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="primary" small> mdi-eye </v-icon>
                        View
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      v-if="can('settings_menu_categories_edit')"
                      @click="editItem(item, false)"
                    >
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="secondary" small> mdi-pencil </v-icon>
                        Edit
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      v-if="can('settings_menu_categories_delete')"
                      @click="deleteItem(item)"
                    >
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="error" small> mdi-delete </v-icon>
                        Delete
                      </v-list-item-title>
                    </v-list-item> -->
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  props: ["SelectedRoomObj"],
  data: () => ({
    roomItemsDialog: false,
    hotel_food_list: [],
    rooms_list: [],
    hotel_food_dropdown_list: [],
    reason_cancelled: "",
    cancelSelectedItem: null,
    cancelItemDialog: false,
    dialogQRcode: false,
    qrCodeImage: "",
    //datatable varables
    page: 1,
    perPage: 0,
    currentPage: 1,
    cumulativeIndex: 1,
    totalTableRowsCount: 0,
    options: {},
    filters: { room_id: "", food_item_id: "", is_non_veg: "", status: "" },
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
        text: "Item Name",
        value: "food.name",
        key: "food.name",
        align: "left",
        sortable: true,
        filterable: true,
      },

      {
        text: "Veg/Non-Veg",
        value: "food.is_non_veg",
        key: "food.is_non_veg",
        align: "center",
        sortable: true,
        filterable: true,
      },

      {
        text: "QTY",
        value: "qty",
        align: "center",
        sortable: true,
        key: "qty",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Delivery Time",
        value: "delivery_datetime",
        align: "left",
        sortable: true,
        key: "delivery_datetime",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Cancelled Time",
        value: "cancelled_datetime",
        align: "left",
        sortable: true,
        key: "cancelled_datetime",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Status",
        value: "status",
        align: "left",
        sortable: true,
        key: "status",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Change",
        value: "action1",
        align: "left",
        sortable: false,
        key: "action1",
        filterable: true,
        filterSpecial: false,
      },

      { text: "Options", value: "options", align: "left", sortable: false },
    ],

    endpoint: "hotel_orders_food",

    newItemDialog: false,

    //add edit item details
    editedItem: {},
    editedItemIndex: -1,

    errors: {},
    snackbar: false,
    snackbarColor: "red",
    snackbarResponse: "",
    viewMode: false,
    floors: [
      1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
    ],
    from_date: "",
    to_date: "",
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
    this.getRoomList();
    this.getHoteFoodDropdownList();

    setInterval(() => {
      if (this.$route.name == "hotel_checkin-orders-food") {
        this.getDataFromApi();
      }
    }, 1000 * 60 * 5);
  },
  methods: {
    viewRoomItems(room_id) {},
    cancelItem(item) {
      if (item.status == 0) {
        this.cancelItemDialog = true;
        this.cancelSelectedItem = item;
      } else if (item.status != 4) {
        alert(
          "Sorry. Order Can Not Be Cancelled. Bceause, Food is already preparing...."
        );
      }
    },
    confirmCancel() {
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          hotel_order_id: this.cancelSelectedItem.id,
          reason_cancelled: this.reason_cancelled,
        },
      };

      this.$axios
        .post(`hotel_order_status_to_cancel`, options.params)
        .then(({ data }) => {
          this.getDataFromApi();
          this.$emit("roomupdates", []);
          this.cancelItemDialog = false;
          this.cancelSelectedItem = null;
        });

      this.cancelSelectedItem = null;
    },

    getRoomList() {
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios.get(`room_dropdown_list`, options).then(({ data }) => {
        this.rooms_list = data;
      });
    },
    getHoteFoodDropdownList() {
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios.get(`hotel_food_dropdown_list`, options).then(({ data }) => {
        this.hotel_food_list = data;
      });
    },
    changeStatusToPreparing(item) {
      if (confirm("Are you sure you want to change To Preparing?")) {
        let options = {
          params: {
            company_id: this.$auth.user.company.id,
            hotel_order_id: item.id,
          },
        };

        this.$axios
          .post(`hotel_order_status_to_preparing`, options.params)
          .then(({ data }) => {
            this.getDataFromApi();
            this.$emit("roomupdates", []);
          });
      }
    },
    changeStatusToDelivered(item) {
      if (confirm("Are you sure you want to change To Completed?")) {
        let options = {
          params: {
            company_id: this.$auth.user.company.id,
            hotel_order_id: item.id,
            item: item,
          },
        };

        this.$axios
          .post(`hotel_order_status_to_delivered`, options.params)
          .then(({ data }) => {
            this.getDataFromApi();
            this.$emit("roomupdates", []);
          });
      }
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      //this.filterType = data.type;
      this.search = data.search;
      if (this.from_date && this.to_date) this.getDataFromApi();
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return (
        year +
        "-" +
        (month < 10 ? "0" : "") +
        month +
        "-" +
        (day < 10 ? "0" : "") +
        day
      );
    },
    displayTimeFormate(inputTime) {
      // Given time in "hh:mm:ss" format
      // const inputTime = "13:10:15";

      // Split the input time into hours, minutes, and seconds
      const [hours, minutes, seconds] = inputTime.split(":");

      // Create a new Date object and set the hours, minutes, and seconds
      const dateObj = new Date();
      dateObj.setHours(hours);
      dateObj.setMinutes(minutes);
      dateObj.setSeconds(seconds);

      // Convert to 12-hour clock format with AM/PM
      const formattedTime = dateObj.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });

      return formattedTime;
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    AddNewCategory() {
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

          filter_room_id: this.SelectedRoomObj.room_id,
          filter_request_datetime: this.SelectedRoomObj.request_datetime,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;

        this.totalTableRowsCount = data.total;

        setTimeout(() => {
          this.loading = false;
        }, 1000);
      });
    },

    viewItem(item) {
      this.editItem(item, true);
    },
    editItem(item, viewMode = false) {
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
            ...this.editedItem,
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
