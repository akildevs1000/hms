<template>
  <div
    v-if="
      can('settings_menu_timings_access') && can('settings_menu_timings_view')
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

    <v-dialog v-model="newItemDialog" max-width="20%">
      <v-card>
        <v-card-title dense class="primary white--text background">
          <span v-if="viewMode">View Time Info </span>
          <span v-else-if="editedItemIndex == -1">Add Time </span>
          <span v-else>Edit Time Info </span>
          <v-spacer></v-spacer>
          <v-icon @click="newItemDialog = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col md="12" cols="12">
                <v-text-field
                  label="Display Name"
                  :disabled="viewMode"
                  v-model="editedItem.name"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  placeholder="Name"
                ></v-text-field>
                <span dense v-if="errors && errors.name" class="error--text">{{
                  errors.name[0]
                }}</span>
              </v-col>
              <v-col md="12" cols="12">
                <v-select
                  label="Open Time*"
                  v-model="editedItem.start_hour"
                  :disabled="viewMode"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  :items="timings"
                  placeholder="Select Open Time"
                ></v-select>
                <span v-if="errors && errors.start_time" class="error--text">{{
                  errors.start_time[0]
                }}</span>
              </v-col>
              <v-col md="12" cols="12">
                <v-select
                  label="Close Time*"
                  v-model="editedItem.end_hour"
                  :disabled="viewMode"
                  outlined
                  dense
                  small
                  :hide-details="true"
                  :items="timings"
                  placeholder="Select Close Time"
                ></v-select>
                <span v-if="errors && errors.end_time" class="error--text">{{
                  errors.end_time[0]
                }}</span>
              </v-col>
            </v-row>

            <v-card-actions class="mt-5" v-if="!viewMode">
              <v-btn @click="newItemDialog = false" dark filled color="red"
                >Cancel</v-btn
              >
              <v-spacer></v-spacer>
              <v-btn @click="save()" dark filled color="primary">Save</v-btn>
            </v-card-actions>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card class="mb-5" elevation="0">
      <v-toolbar
        class="rounded-md mb-2 white--text"
        color="background"
        dense
        flat
      >
        <!-- <v-toolbar-title><span>menu_timings</span></v-toolbar-title> -->
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
              <v-icon color="white" class="ml-2" @click="reload()" dark
                >mdi mdi-reload</v-icon
              >
            </v-btn>
          </template>
          <span>Reload</span>
        </v-tooltip>
        <v-tooltip top color="primary">
          <!-- <template v-slot:activator="{ on, attrs }">
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
          <span>Filter</span> -->
        </v-tooltip>
        <v-spacer></v-spacer>
        <v-tooltip
          v-if="can('settings_menu_timings_create')"
          top
          color="primary"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              x-small
              :ripple="false"
              text
              v-bind="attrs"
              v-on="on"
              @click="AddNewTime()"
            >
              <v-icon color="white" dark white>mdi-plus-circle</v-icon>
            </v-btn>
          </template>
          <span>Add Time Details</span>
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
            v-sortable-data-table
            @sorted="saveOrder"
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
            <template v-slot:item.name="{ item }"> {{ item.name }}</template>
            <template v-slot:item.time="{ item }">
              {{ item.start_hour }}.00 To {{ item.end_hour }}.00</template
            >

            <template
              v-slot:item.options="{ item }"
              v-if="
                can('settings_menu_timings_view') ||
                can('settings_menu_timings_edit') ||
                can('settings_menu_timings_delete')
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
                    v-if="can('settings_menu_timings_view')"
                    @click="editItem(item, true)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small> mdi-eye </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('settings_menu_timings_edit')"
                    @click="editItem(item, false)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('settings_menu_timings_delete')"
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
        text: "Name",
        value: "name",
        align: "left",
        sortable: true,
        filterable: true,
      },
      {
        text: "Open and Close Timings",
        value: "time",
        align: "left",
        sortable: true,
        key: "time",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Display Order",
        value: "display_order",
        align: "left",
        sortable: true,
        key: "display_order",
        filterable: true,
        filterSpecial: false,
      },
      { text: "Options", value: "options", align: "left", sortable: false },
    ],

    endpoint: "hotel_food_timings",

    newItemDialog: false,

    //add edit item details
    editedItem: {},
    editedItemIndex: -1,

    errors: {},
    snackbar: false,
    snackbarColor: "red",
    snackbarResponse: "",
    viewMode: false,
    timings: [
      1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21,
      22, 23, 24,
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
  },
  directives: {
    sortableDataTable: {
      bind(el, binding, vnode) {
        const options = {
          animation: 150,
          onUpdate: function (event) {
            vnode.child.$emit("sorted", event);
          },
        };
        Sortable.create(el.getElementsByTagName("tbody")[0], options);
      },
    },
  },
  methods: {
    saveOrder(event) {
      const movedItem = this.data.splice(event.oldIndex, 1)[0];
      this.data.splice(event.newIndex, 0, movedItem);

      let finalDisplayOrder = [];
      let display_order = 1;
      this.data.forEach((element) => {
        finalDisplayOrder.push({
          id: element.id,
          display_order: display_order,
        });
        display_order = display_order + 1;
      });

      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          order_items: finalDisplayOrder,
        },
      };

      this.$axios
        .post(`menu_timings_display_order_update`, options.params)
        .then(({ data }) => {
          this.getDataFromApi();
        });
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    AddNewTime() {
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
