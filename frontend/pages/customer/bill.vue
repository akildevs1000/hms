<template>
  <div v-if="can(`customer_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>
    <v-row>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-autocomplete
          class="custom-text-box shadow-none"
          @change="getDataFromApi(`booking_customers`)"
          v-model="pagination.per_page"
          :items="[50, 100, 500, 1000]"
          placeholder="Per Page Records"
          solo
          flat
          :hide-details="true"
        ></v-autocomplete>
      </v-col>
      <!-- <v-col xs="12" sm="12" md="3" cols="12">
        <input
          class="form-control py-3 custom-text-box floating shadow-none"
          placeholder="Search..."
          @input="searchIt"
          v-model="search"
          type="text"
        />
      </v-col> -->
    </v-row>

    <div v-if="can(`customer_view`)">
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
          <span> {{ Model }} List</span>
        </v-toolbar>
        <table>
          <tr>
            <th v-for="(item, index) in headers" :key="index">
              {{ item.text }}
            </th>
          </tr>
          <v-progress-linear
            v-if="loading"
            :active="loading"
            :indeterminate="loading"
            absolute
            color="primary"
          ></v-progress-linear>
          <tr v-for="(item, index) in data" :key="index">
            <td>
              <b>{{ ++index }}</b>
            </td>
            <td>{{ item.full_name }}</td>
            <td>{{ item.contact_no }}</td>
            <td>{{ item.email }}</td>
            <td>{{ item.id_card_type_id }}</td>
            <td>{{ item.id_card_no }}</td>
            <td>{{ item.car_no }}</td>
            <td>{{ item.no_of_adult }}</td>
            <td>{{ item.no_of_child }}</td>
            <td>{{ item.no_of_baby }}</td>
            <td>{{ item.address }}</td>
            <td>
              <v-icon
                @click="viewCustomerBilling(item.id)"
                x-small
                color="primary"
                class="mr-2"
              >
                mdi-eye
              </v-icon>
            </td>
          </tr>
        </table>
      </v-card>
      <div>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination
                v-model="pagination.current"
                :length="pagination.total"
                @input="onPageChange"
                :total-visible="12"
              ></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    Model: "Customer",
    endpoint: "booking_customers",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      {
        text: "#"
      },
      {
        text: "Name"
      },
      {
        text: "Contact"
      },

      {
        text: "Email"
      },
      {
        text: "Id Card Type"
      },
      {
        text: "Id Card"
      },
      {
        text: "Car No."
      },
      {
        text: "Adults"
      },
      {
        text: "Childs"
      },
      {
        text: "Babies"
      },

      {
        text: "Address"
      },
      {
        text: "Action"
      }
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: []
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    }
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

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    viewCustomerBilling(id) {
      this.$router.push(`/customer/details/${id}`);
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id
        }
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
    }
  }
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
