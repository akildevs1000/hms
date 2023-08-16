<template>
  <div v-if="can(`guest_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="NewCustomerDialog" max-width="60%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Create Customer</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="NewCustomerDialog = false">mdi mdi-close-box</v-icon>
        </v-toolbar>
        <v-container>
          <CreateCustomer @close-dialog="closeDialogs" />
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="viewCustomerDialog" max-width="60%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Edit Customer</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="viewCustomerDialog = false">mdi mdi-close-box</v-icon>
        </v-toolbar>
        <v-container class="mt-0 pt-0">
          <customer-index :edit_mode="true" :customer_id="customer_id" @close-dialog="closeDialogs" />
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row class="mt-1 mb-0">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>
    <v-row class="mt-0 pt-0">
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" @input="searchIt"
          v-model="search" hide-details></v-text-field>
      </v-col>
    </v-row>

    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md mb-2 white--text" color="background" dense flat>
        <v-toolbar-title><span>{{ Model }} List</span></v-toolbar-title>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
              <v-icon color="white" class="ml-2" @click="reload()" dark>mdi
                mdi-reload</v-icon>
            </v-btn>
          </template>
          <span>Reload</span>
        </v-tooltip>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="toggleFilter">
              <v-icon white color="#FFF">mdi-filter</v-icon>
            </v-btn>
          </template>
          <span>Filter</span>
        </v-tooltip>
        <v-spacer></v-spacer>
        <v-tooltip v-if="can('guest_create')" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="NewCustomerDialog = true">
              <v-icon color="white" dark white>mdi-plus-circle</v-icon>
            </v-btn>
          </template>
          <span>Add New Room</span>
        </v-tooltip>
      </v-toolbar>
      <v-row>
        <v-col cols="12">
          <v-data-table dense :headers="headers_table" :items="data" :loading="loading" :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [20, 50, 100, 500, 1000],
            }" class="elevation-1" :server-items-length="totalTableRowsCount">
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-text-field clearable :hide-details="true" v-if="header.filterable && !header.filterSpecial"
                    v-model="filters[header.key]" :id="header.value" @input="applyFilters(header.key, $event)" outlined
                    dense autocomplete="off"></v-text-field>

                </td>
              </tr>


            </template>
            <template v-slot:item.sno="{ item, index }">
              {{ currentPage ? ((currentPage - 1) * perPage) + (cumulativeIndex + itemIndex(item)) : '' }}
            </template>
            <template v-slot:item.first_name="{ item }">
              {{ item.full_name }}
            </template>
            <template v-slot:item.email="{ item }">
              {{ item.email || '---' }}
            </template>
            <template v-slot:item.contact_no="{ item }">
              {{ item.contact_no || '---' }}
            </template>
            <template v-slot:item.id_card_type.name="{ item }">
              {{ item.id_card_type ? item.id_card_type.name : '---' }}
            </template>
            <template v-slot:item.id_card_no="{ item }">
              {{ item.id_card_no || '---' }}
            </template>
            <template v-slot:item.car_no="{ item }">
              {{ item.car_no || '---' }}
            </template>
            <template v-slot:item.gst="{ item }">
              {{ item.gst || '---' }}
            </template>
            <template v-slot:item.address="{ item }">
              {{ item.address || '---' }}
            </template>
            <template v-slot:item.options="{ item }">
              <v-icon x-small v-if="can('guest_edit')" color="primary" @click="viewCustomerBilling(item)" class="mr-2">
                mdi-pencil
              </v-icon>
            </template>


          </v-data-table>
        </v-col>
      </v-row>


      <!-- <table>
        <tr style="font-size: 13px">
          <th v-for="(item, index) in headers" :key="index">
            {{ item.text }}
          </th>
        </tr>
        <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
          color="primary"></v-progress-linear>
        <tr v-for="(item, index) in data" :key="index" style="font-size: 13px">
          <td>
            <b>{{ ++index }}</b>
          </td>
          <td>{{ item.full_name || "---" }}</td>
          <td>{{ item.contact_no || "---" }}</td>
          <td>{{ item.email || "---" }}</td>
          <td>
            {{ (item.id_card_type && item.id_card_type.name) || "---" }}
          </td>
          <td>{{ item.id_card_no || "---" }}</td>
          <td>{{ item.car_no || "---" }}</td>

          <td>{{ item.gst_number || "---" }}</td>
          <td>{{ item.address || "---" }}</td>
          <td>
            <v-icon x-small v-if="can('guest_edit')" color="primary" @click="viewCustomerBilling(item)" class="mr-2">
              mdi-pencil
            </v-icon>
          </td>
        </tr>
      </table>
   
    <div>
      <v-row>
        <v-col md="12" class="float-right">
          <div class="float-right">
            <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
              :total-visible="12"></v-pagination>
          </div>
        </v-col>
      </v-row>

  </div>-->
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
import CreateCustomer from "../../components/customer/CreateCustomer.vue";
import CustomerIndex from "../../components/customer/CustomerIndex.vue";
export default {
  components: {
    CustomerIndex,
    CreateCustomer,
  },
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
    headers_table: [{ text: "#", value: "sno", align: "left", sortable: false, filterable: false, },
    { text: "Contact", value: "first_name", align: "left", key: 'first_name', sortable: true, filterable: true, },
    { text: "Email", value: "email", align: "left", sortable: true, key: 'email', filterable: true, filterSpecial: false },
    { text: "Id Card Type", value: "id_card_type.name", key: "id_card_type_name", align: "left", sortable: true, filterable: true, filterSpecial: true, width: '150px' },
    { text: "Id Card  ", value: "id_card_no", key: "id_card_no", align: "left", sortable: true, filterable: true, filterSpecial: false },
    { text: "Car No.  ", value: "car_no", key: "car_no", align: "left", sortable: true, filterable: true, filterSpecial: false },
    { text: "GST", value: "gst_number", align: "left", key: "gst_number", sortable: true, filterable: true, filterSpecial: false },
    { text: "Address", value: "address", align: "left", key: "address", sortable: true, filterable: true, filterSpecial: false },

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
    NewCustomerDialog: false,
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
    }


  },

  methods: {
    onPageChange() {
      this.getDataFromApi();
    },

    closeDialogs() {
      this.getDataFromApi();
      this.NewCustomerDialog = false;
      this.viewCustomerDialog = false;
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    viewCustomerBilling(item) {
      // this.$router.push(`/customer/history/${item.id}`);
      this.customer_id = item.id;
      this.viewCustomerDialog = true;
    },
    applyFilters() {
      this.$set(this.options, 'page', 1);
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
      this.$set(this.options, 'page', 1);
      //this.$set(this.options, 'sortBy', '');

      this.getDataFromApi(this.endpoint, 1);
    },
    getDataFromApi(url = this.endpoint, customPage = 0) {
      this.loading = true;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : '';
      let sortedDesc = sortDesc ? sortDesc[0] : '';
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

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
        this.totalTableRowsCount = data.total;
      });
    },

    searchIt() {
      let s = this.search.length;
      let search = this.search;
      if (s == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (s > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${search}`);
      }
    },
  },
};
</script>
<!-- 
<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
  height: 50px;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}
</style> -->
