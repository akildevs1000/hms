<template>
  <div
    v-if="
      can('settings_hotel_menu_items_access') &&
      can('settings_hotel_menu_items_view')
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

    <v-dialog v-model="newItemDialog" max-width="60%">
      <v-card>
        <v-card-title dense class="primary white--text background">
          <span v-if="viewMode">View Item Info </span>
          <span v-else-if="editedItemIndex == -1">Add Menu Item </span>
          <span v-else>Edit Item Info </span>
          <v-spacer></v-spacer>
          <v-icon @click="newItemDialog = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="6">
                <v-row>
                  <v-col md="12" cols="12">
                    <v-text-field
                      label="Name*"
                      :disabled="viewMode"
                      v-model="editedItem.name"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      placeholder="Name"
                    ></v-text-field>
                    <span
                      dense
                      v-if="errors && errors.name"
                      class="error--text"
                      >{{ errors.name[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-select
                      label="Category*"
                      v-model="editedItem.category_id"
                      :disabled="viewMode"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      :items="categories"
                      item-text="name"
                      item-value="id"
                      placeholder="Select Category"
                    ></v-select>
                    <span
                      v-if="errors && errors.category_id"
                      class="error--text"
                      >{{ errors.category_id[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-select
                      multiple
                      label="Availalbe Time*"
                      v-model="editedItem.timing_id"
                      :disabled="viewMode"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      :items="timings"
                      placeholder="Select Time"
                      item-text="name"
                      item-value="id"
                    ></v-select>
                    <span
                      v-if="errors && errors.timing_id"
                      class="error--text"
                      >{{ errors.timing_id[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-select
                      label="Veg or Non-Veg*"
                      v-model="editedItem.is_non_veg"
                      :disabled="viewMode"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      :items="[
                        { name: 'Veg', id: 0 },
                        { name: 'Non-Veg', id: 1 },
                      ]"
                      placeholder="Select Veg or Non-Veg"
                      item-text="name"
                      item-value="id"
                    ></v-select>
                    <span
                      v-if="errors && errors.is_non_veg"
                      class="error--text"
                      >{{ errors.is_non_veg[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-text-field
                      label="Price*"
                      :disabled="viewMode"
                      v-model="editedItem.amount"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      placeholder="Price"
                    ></v-text-field>
                    <span
                      dense
                      v-if="errors && errors.amount"
                      class="error--text"
                      >{{ errors.amount[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-text-field
                      label="Ingredients"
                      :disabled="viewMode"
                      v-model="editedItem.ingredients"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      placeholder="Name"
                    ></v-text-field>
                    <span
                      dense
                      v-if="errors && errors.ingredients"
                      class="error--text"
                      >{{ errors.ingredients[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-textarea
                      label="Item Description"
                      :disabled="viewMode"
                      v-model="editedItem.description"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      placeholder="Item Description"
                    ></v-textarea>
                    <span
                      dense
                      v-if="errors && errors.description"
                      class="error--text"
                      >{{ errors.description[0] }}</span
                    >
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-select
                      label="Status*"
                      v-model="editedItem.status"
                      :disabled="viewMode"
                      outlined
                      dense
                      small
                      :hide-details="true"
                      :items="[
                        { name: 'Availalbe', id: 1 },
                        { name: 'Out of Stock', id: 0 },
                      ]"
                      placeholder="Select Veg or Non-Veg"
                      item-text="name"
                      item-value="id"
                    ></v-select>
                    <span v-if="errors && errors.status" class="error--text">{{
                      errors.status[0]
                    }}</span>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="6">
                <div class="form-group" style="margin: 0 auto; width: 200px">
                  <v-img
                    style="
                      width: 100%;
                      height: 200px;
                      border: 1px solid #5fafa3;
                      border-radius: 10%;
                      margin: 0 auto;
                    "
                    :src="previewImage || '/noimage.png'"
                  ></v-img>
                  <br />
                  <v-btn
                    small
                    class="form-control primary"
                    @click="onpick_attachment"
                    >{{ !upload.name ? "Upload" : "Change" }} Menu Image
                    <v-icon right dark>mdi-cloud-upload</v-icon>
                  </v-btn>
                  <input
                    required
                    type="file"
                    @change="attachment"
                    style="display: none"
                    accept="image/*"
                    ref="attachment_input"
                  />

                  <span
                    v-if="errors && errors.profile_picture"
                    class="error--text mt-2"
                    >{{ errors.profile_picture[0] }}</span
                  >
                </div>
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

    <!-- <v-row>
      <v-col cols="2">
        <v-autocomplete
          outlined
          dense
          v-model="filter_category_id"
          :items="categories"
          item-value="value"
          item-text="name"
          :hide-details="true"
          clearable
          @change="apply_filters"
        ></v-autocomplete>
      </v-col>
    </v-row> -->

    <v-card class="mb-5" elevation="0">
      <v-toolbar
        class="rounded-md mb-2 white--text"
        color="background"
        dense
        flat
      >
        <!-- <v-toolbar-title><span>hotel_menu_items</span></v-toolbar-title> -->
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
        <v-tooltip
          v-if="can('settings_hotel_menu_items_create')"
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
              @click="AddNewItem()"
            >
              <v-icon color="white" dark white>mdi-plus-circle</v-icon>
            </v-btn>
          </template>
          <span>Add Menu Item</span>
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
            v-sortable-data-table
            @sorted="saveOrder"
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
            <template v-slot:item.name="{ item }"> {{ item.name }}</template>
            <template v-slot:item.description="{ item }">
              {{ item.description }}</template
            >
            <template v-slot:item.category_id="{ item }">
              {{ item.category?.name }}
            </template>
            <template v-slot:item.timing_id="{ item }">
              {{ item.timing?.name }}</template
            >

            <template v-slot:item.is_non_veg="{ item }">
              <v-icon :color="item.is_non_veg == 1 ? 'red' : 'green'"
                >mdi mdi-square-circle</v-icon
              >
            </template>

            <template v-slot:item.status="{ item }">
              <v-switch
                v-model="item.status"
                color="green"
                @change="changeItemStatus(item, $event)"
              ></v-switch>
            </template>
            <template
              v-slot:item.options="{ item }"
              v-if="
                can('settings_hotel_menu_items_view') ||
                can('settings_hotel_menu_items_edit') ||
                can('settings_hotel_menu_items_delete')
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
                    v-if="can('settings_hotel_menu_items_view')"
                    @click="editItem(item, true)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small> mdi-eye </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('settings_hotel_menu_items_edit')"
                    @click="editItem(item, false)"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    v-if="can('settings_hotel_menu_items_delete')"
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
    filter_category_id: "",
    upload: {
      name: "",
    },
    previewImage: null,
    selectedFile: "",
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
        text: "Category",
        value: "category_id",
        align: "left",
        sortable: true,
        key: "category_id",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Veg/Non-Veg",
        value: "is_non_veg",
        align: "left",
        sortable: true,
        key: "is_non_veg",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Serve Time",
        value: "timing_id",
        align: "left",
        sortable: true,
        key: "timing_id",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Price",
        value: "amount",
        align: "left",
        sortable: true,
        key: "amount",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Display Status",
        value: "status",
        align: "left",
        sortable: true,
        key: "status",
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

    endpoint: "hotel_food_items",
    timings: [],
    categories: [],
    newItemDialog: false,

    //add edit item details
    editedItem: { timing_id: [] },
    editedItemIndex: -1,

    errors: {},
    snackbar: false,
    snackbarColor: "red",
    snackbarResponse: "",
    viewMode: false,
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
    this.getCategoriesList();
    this.getTimingsList();
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
        .post(`menu_food_items_display_order_update`, options.params)
        .then(({ data }) => {
          this.getDataFromApi();
        });
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },
    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;

      if (file[0].size > 1024 * 1024) {
        e.preventDefault();
        this.errors["profile_picture"] = [
          "File too big (> 1MB). Upload less than 1MB",
        ];
        return;
      }

      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
          this.selectedFile = event.target.result;
          //this.$refs.cropper.replace(this.selectedFile);
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);

        this.dialogCropping = true;
      }
    },
    changeItemStatus(item, status) {
      //console.log(roomId, status);
      this.editedItemIndex = item.id;
      this.editedItem = item;
      this.editedItem.status = status ? 1 : 0;
      this.save();
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    AddNewItem() {
      this.previewImage = false;
      this.getCategoriesList();
      this.getTimingsList();
      this.editedItem = {};
      this.editedItemIndex = -1;
      this.viewMode = false;
      this.newItemDialog = true;
    },

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
    getCategoriesList() {
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.$axios
        .get(`get_hotel_menu_categories_list`, options)
        .then(({ data }) => {
          this.categories = data;
        });
    },
    getTimingsList() {
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.$axios
        .get(`get_hotel_menu_timings_list`, options)
        .then(({ data }) => {
          this.timings = data;
        });
    },
    viewItem(item) {
      this.editItem(item, true);
    },
    editItem(item, viewMode = false) {
      this.getCategoriesList();
      this.getTimingsList();
      this.viewMode = viewMode;
      this.editedItem = {};
      let options = { params: { company_id: this.$auth.user.company.id } };
      this.newItemDialog = true;

      this.$axios
        .get(`${this.endpoint}/${item.id}`, options)
        .then(({ data }) => {
          this.editedItem = data;

          let timings_arr = data.timing_id.split(",");
          this.editedItem.timing_id = [];
          timings_arr.forEach((element) => {
            this.editedItem.timing_id.push(parseInt(element));
          });
          timings_arr.push(1);

          this.editedItemIndex = item.id;
          this.previewImage = item.item_picture;
        });
    },
    save() {
      let payload = new FormData();

      if (this.upload.name) {
        payload.append("image", this.upload.name);
      }
      payload.append("company_id", this.$auth.user.company.id);
      payload.append("name", this.editedItem.name);
      payload.append("category_id", this.editedItem.category_id);
      payload.append("timing_id", this.editedItem.timing_id);
      payload.append("is_non_veg", this.editedItem.is_non_veg);
      payload.append("amount", this.editedItem.amount);
      payload.append("ingredients", this.editedItem.ingredients ?? "");
      payload.append("description", this.editedItem.description ?? "");
      payload.append("status", this.editedItem.status);

      if (this.editedItemIndex != -1) {
        payload.append("_method", "PUT");

        this.$axios
          .post(`${this.endpoint}/${this.editedItemIndex}`, payload)
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
        // let options = {
        //   params: {
        //     company_id: this.$auth.user.company.id,
        //     user_id: this.$auth.user.id,
        //     ...this.editedItem,
        //     image: this.upload.name,
        //   },
        // };
        // // if (this.upload.name) {
        // //   payload.append("image", this.upload.name);
        // // }
        payload.append("_method", "POST");
        this.$axios.post(`${this.endpoint}`, payload).then(({ data }) => {
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
