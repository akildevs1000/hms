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
          <customer-index :customer_id="customer_id" @close-dialog="closeDialogs" />
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
    <div>
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span> {{ Model }} List </span>
          <v-spacer></v-spacer>
          <v-btn v-if="can('guest_create')" class="float-right py-3" @click="NewCustomerDialog = true" x-small
            color="primary">
            <v-icon color="white" small class="py-5">mdi-plus</v-icon>
            Add
          </v-btn>
        </v-toolbar>
        <table>
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
      </v-card>
      <div>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                :total-visible="12"></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>

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

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
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
</style>
