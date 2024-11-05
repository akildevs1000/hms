<template>
  <div v-if="can(`guest_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div>
      <v-row>
        <v-col cols="3">
          <v-card style="min-height: 598px; max-height: 598px">
            <div dense flat class="text-color">
              <v-container> Customers </v-container>
            </div>
            <div class="mx-3">
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
            </div>
            <v-list
              dense
              style="
                min-height: 500px;
                overflow-y: scroll;
                max-height: 500px;
                overflow-y: scroll;
              "
            >
              <v-list-item
                v-for="(item, index) in data"
                :key="index"
                @click="selectCustomer(item)"
              >
                <v-avatar
                  v-if="item.captured_photo"
                  class="mr-2"
                  color="purple"
                  size="30"
                >
                  <v-img :src="item.captured_photo"></v-img>
                </v-avatar>
                <v-avatar v-else class="mr-2" color="purple" size="30">
                  <span class="white--text">C</span>
                </v-avatar>
                <v-list-item-content>
                  <v-list-item-title>{{ item.full_name }}</v-list-item-title>
                  <v-list-item-subtitle>{{
                    item.whatsapp
                  }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
        <v-col cols="9">
          <v-card style="min-height: 598px; max-height: 598px">
            <div dense flat class="grey lighten-3">
              <v-container>
                <v-avatar
                  v-if="selectedCustomer.captured_photo"
                  class="mr-2"
                  color="purple"
                  size="30"
                >
                  <v-img :src="selectedCustomer.captured_photo"></v-img>
                </v-avatar>
                <v-avatar v-else class="mr-2" color="purple" size="30">
                  <span class="white--text">C</span>
                </v-avatar>

                {{ selectedCustomer?.full_name || "Customer" }}
              </v-container>
            </div>

            <v-card-text>
              <CustomerChat
                v-if="selectedCustomer && selectedCustomer.id"
                :key="selectedCustomer.id"
                :id="selectedCustomer.id"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
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
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    selectedCustomer: null,
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
    selectCustomer(item) {
      this.selectedCustomer = item;
    },
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
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
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
