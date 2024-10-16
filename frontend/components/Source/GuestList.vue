<template>
  <span>
    <v-dialog v-model="NewCustomerDialog" :width="activeTab == 0 ? 800 : 1100">
      <AssetsIconClose
        :left="activeTab == 0 ? 790 : 1090"
        @click="NewCustomerDialog = false"
      />
      <template v-slot:activator="{ on, attrs }">
        <span v-bind="attrs" v-on="on" small>
          <v-icon x-small color="black">mdi-file</v-icon>
          <AssetsTextLabel color="text-color" label="Details" />
        </span>
      </template>

      <v-card>
        <v-tabs
          v-model="activeTab"
          background-color="grey lighten-3"
          dense
          rounded
          right
        >
          <div class="pa-3">
            <span>Source Details</span>
          </div>
          <v-spacer></v-spacer>
          <v-tab>Overview </v-tab>
          <v-tab>Transactions </v-tab>
          <v-tab>Statement </v-tab>
          <v-tab>Customers </v-tab>
          <v-tab-item>
            <div
              v-if="can(`guest_access`)"
              style="max-height: 500px; overflow-y: scroll; overflow-x: hidden"
            >
              <v-container>
                <v-row v-if="item && item.id">
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="item.name"
                      placeholder="Company"
                      :hide-details="true"
                      outlined
                      label="Company"
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="item.contact_name"
                      placeholder="Name"
                      label="Name"
                      outlined
                      :hide-details="true"
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="6">
                    <v-text-field
                      v-model="item.mobile"
                      placeholder="Mobile"
                      label="Mobile"
                      outlined
                      dense
                      :hide-details="true"
                      type="number"
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="item.landline"
                      placeholder="Landline"
                      outlined
                      label="Landline"
                      dense
                      :hide-details="true"
                      type="number"
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="item.email"
                      placeholder="Email"
                      outlined
                      label="Email"
                      dense
                      :hide-details="true"
                      type="email"
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-text-field
                      v-model="item.gst"
                      placeholder="GST"
                      label="GST"
                      :hide-details="true"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col md="12" cols="12">
                    <v-text-field
                      v-model="item.address"
                      placeholder="Address"
                      outlined
                      label="Address"
                      textarea
                      hide-details
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </div>
            <NoAccess v-else />
          </v-tab-item>
          <v-tab-item>
            <v-container>
              <SourceTransactions :source_id="item.id" />
            </v-container>
          </v-tab-item>
          <v-tab-item>
            <v-container>
              <SourceStatement
                :item="item"
                :source_id="item.id"
              ></SourceStatement>
            </v-container>
          </v-tab-item>
          <v-tab-item>
            <v-container>
              <v-row class="px-2 pb-2">
                <v-col cols="2">
                  <v-text-field
                    class="global-search-textbox"
                    append-icon="mdi-magnify"
                    label="Search..."
                    clearable
                    dense
                    outlined
                    hide-details
                    @input="searchIt"
                    v-model="search"
                  ></v-text-field>
                </v-col>
                <v-col class="text-right">
                  <!-- <CustomerCreate @close-dialog="closeDialogs" /> -->
                  <!-- <FilterDateRange height="30" @filter-attr="filterAttr" /> -->
                </v-col>
              </v-row>

              <v-data-table
                dense
                :headers="headers_table"
                :items="data"
                :loading="loading"
                :options.sync="options"
                :footer-props="{
                  itemsPerPageOptions: [20, 50, 100, 500, 1000],
                }"
                class="px-2"
                :server-items-length="totalTableRowsCount"
              >
                <template v-slot:item.sno="{ item, index }">
                  <small class="text-color">{{
                    currentPage
                      ? (currentPage - 1) * perPage +
                        (cumulativeIndex + itemIndex(item))
                      : ""
                  }}</small>
                </template>
                <template v-slot:item.first_name="{ item }">
                  <small class="text-color">{{ item.full_name }}</small>
                </template>
                <template v-slot:item.email="{ item }">
                  <small class="text-color"> {{ item.email || "---" }}</small>
                </template>
                <template v-slot:item.contact_no="{ item }">
                  <small class="text-color">{{
                    item.contact_no || "---"
                  }}</small>
                </template>
                <template v-slot:item.whatsapp="{ item }">
                  <small class="text-color">{{ item.whatsapp || "---" }}</small>
                </template>
                <template v-slot:item.address="{ item }">
                  <!-- {{ item.city }},{{ item.state }},{{ item.zip_code }} ,{{
                  item.country
                }} -->
                  <small class="text-color">
                    <span v-if="item.city">{{ item.city }}</span>
                    <span v-if="item.city && item.state">, </span>
                    <span v-if="item.state">{{ item.state }}</span>
                    <span v-if="item.state && item.zip_code">
                      {{ item.zip_code }}</span
                    >
                    <span
                      v-if="
                        (item.city || item.state || item.zip_code) &&
                        item.country
                      "
                      >,
                    </span>
                    <span v-if="item.country">{{ item.country }}</span>
                  </small>
                  <!-- <small
                  v-if="
                    !item.city && !item.state && !item.zip_code && !item.country
                  "
                  class="text-color"
                >
                  ---</small
                > -->
                </template>
                <template v-slot:item.id_card_type.name="{ item }">
                  <small class="text-color">
                    {{
                      item.id_card_type ? item.id_card_type.name : "---"
                    }}</small
                  >
                </template>
                <template v-slot:item.id_card_no="{ item }">
                  <small class="text-color">
                    {{ item.id_card_no || "---" }}</small
                  >
                </template>
                <template v-slot:item.car_no="{ item }">
                  <small class="text-color">{{ item.car_no || "---" }}</small>
                </template>
                <template v-slot:item.gst="{ item }">
                  <small class="text-color">{{ item.gst || "---" }}</small>
                </template>
                <template v-slot:item.options="{ item }">
                  <v-menu bottom left>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn dark-2 icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list width="80" dense>
                      <v-list-item>
                        <v-list-item-title style="cursor: pointer">
                          <CustomerEdit
                            @close-dialog="closeDialogs"
                            :item="item"
                          />
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-title style="cursor: pointer">
                          <CustomerView :item="item" />
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-title style="cursor: pointer">
                          <CustomerDetails :item="item" />
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </v-data-table>
            </v-container>
          </v-tab-item>
        </v-tabs>
      </v-card>
    </v-dialog>
  </span>
</template>
<script>
export default {
  props: ["customer_type", "item"],
  data: () => ({
    activeTab: 0,
    NewCustomerDialog: false,
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
        text: "Customer",
        value: "first_name",
        align: "left",
        key: "first_name",
        sortable: true,
        filterable: true,
      },
      {
        text: "Source Type",
        value: "customer_type",
        align: "left",
        key: "customer_type",
        sortable: true,
        filterable: true,
      },
      {
        text: "Email",
        value: "email",
        align: "left",
        sortable: true,
        key: "email",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Contact",
        value: "contact_no",
        key: "contact_no",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
        width: "150px",
      },
      {
        text: "Whatsapp",
        value: "whatsapp",
        key: "whatsapp",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
        width: "150px",
      },
      {
        text: "Address",
        value: "address",
        key: "address",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
        width: "150px",
      },
      { text: "Options", value: "options", align: "left", sortable: false },
    ],

    pagination: {
      current: 1,
      total: 0,
      per_page: 30,
    },
    options: {},
    Model: "Customer",
    endpoint: "customer",
    search: "",
    snackbar: false,
    viewCustomerDialog: false,
    dialog: false,
    ids: [],
    loading: false,
    customer_id: "",
    total: 0,
    headers: [
      {
        text: "#",
      },
      {
        text: "Name",
      },
      {
        text: "Contact",
      },

      {
        text: "Email",
      },
      {
        text: "Id Card Type",
      },
      {
        text: "Id Card",
      },
      {
        text: "Car No.",
      },
      {
        text: "GST",
      },
      {
        text: "Address",
      },
      {
        text: "Action",
      },
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },
  created() {
    this.loading = true;
  },
  mounted() {
    this.getDataFromApi();
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },

  methods: {
    handleSelectedCustomer({ customer }) {
      this.customer = customer;
    },
    onPageChange() {
      this.getDataFromApi();
    },

    closeDialogs() {
      this.$emit("response");
      this.getDataFromApi();
      this.viewCustomerDialog = false;
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    viewCustomerBilling(item) {
      // this.$router.push(`/customer/history/${item.id}`);
      this.customer_id = item.id;
      this.viewCustomerDialog = true;
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
      //this.$set(this.options, 'sortBy', '');

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
          customer_type: this.item.type,
          source_id: this.item.id,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
        this.totalTableRowsCount = data.total;
      });
    },

    searchIt() {
      let search = this.search;
      if (search && search.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${search}`);
        return;
      }

      this.getDataFromApi(this.endpoint);
    },
  },
};
</script>
