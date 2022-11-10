<template>
  <div>
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
      <v-col cols="6"> </v-col>
    </v-row>

    <v-row>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="form-control"
          @change="getDataFromApi(`room`)"
          v-model="pagination.per_page"
          :items="[10, 25, 50, 100]"
          placeholder="Per Page Records"
          solo
          hide-details
          flat
        ></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="form-control"
          @change="getDataFromApi(`room`)"
          v-model="pagination.status"
          :items="[
            { id: '-1', name: 'Select All' },
            { id: '0', name: 'Available Room' },
            { id: '1', name: 'Booked Room' },
            { id: '2', name: 'Checked In Room' },
            { id: '3', name: 'Checked Out Room' },
            { id: '4', name: 'DirtyRoom' }
          ]"
          item-text="name"
          item-value="id"
          placeholder="Status"
          solo
          hide-details
          flat
        ></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-text-field
          class="form-control py-0 custom-text-box floating shadow-none"
          placeholder="Search..."
          solo
          flat
          @input="searchIt"
          v-model="search"
          hide-details
        ></v-text-field>
      </v-col>
    </v-row>
    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> {{ Model }} List</span>
      </v-toolbar>
      <table>
        <tr>
          <th v-for="(item, index) in headers" :key="index">
            <span v-html="item.text"></span>
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
          <td>{{ caps(item.room_no) }}</td>
          <td>{{ caps(item.room_type_id) }}</td>
          <td>
            <v-btn
              style="background: linear-gradient(135deg, #23bdb8 0, #65a986 100%) !important;"
              small
              elevation="0"
              dark
              class="l-bg-green-dark"
              v-if="item.status == 0"
            >
              Available Room
            </v-btn>
            <v-btn
              style="background: linear-gradient(135deg, #f48665 0%, #fda23f 100%) !important;"
              small
              elevation="0"
              dark
              color="l-bg-purple-dark"
              v-else-if="item.status == 1"
            >
              Booked
            </v-btn>
            <v-btn
              v-else-if="item.status == 2"
              small
              elevation="0"
              dark
              color="warning"
              >Checked In</v-btn
            >

            <v-btn v-else-if="item.status == 3" small elevation="0" dark
              >Checked Out</v-btn
            >

            <v-btn v-else small elevation="0" dark color="grey">Dirty</v-btn>
          </td>
          <!-- <td>
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn dark-2 icon v-bind="attrs" v-on="on">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list width="120" dense>
                <v-list-item>
                  <v-list-item-title style="cursor:pointer">
                    <v-icon color="secondary" small>
                      mdi-pencil
                    </v-icon>
                    Edit
                  </v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <v-list-item-title style="cursor:pointer">
                    <v-icon color="error" small>
                      mdi-delete
                    </v-icon>
                    Delete
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </td> -->
        </tr>
      </table>
    </v-card>
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
</template>
<script>
export default {
  data: () => ({
    Model: "Room",
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
      status: "-1"
    },
    options: {},
    endpoint: "room",
    search: "",
    snackbar: false,
    dialog: false,
    data: [
      // {
      //   room_no: "101",
      //   room_type: "delux",
      //   booking_status: true,
      //   check_in: false,
      //   check_out: true
      // },
      // {
      //   room_no: "201",
      //   room_type: "delux",
      //   booking_status: false,
      //   check_in: false,
      //   check_out: false
      // },
      // {
      //   room_no: "302",
      //   room_type: "delux",
      //   booking_status: true,
      //   check_in: false,
      //   check_out: false
      // },
      // {
      //   room_no: "504",
      //   room_type: "delux",
      //   booking_status: true,
      //   check_in: true,
      //   check_out: false
      // }
    ],
    loading: false,
    total: 0,
    headers: [{ text: "Room" }, { text: "Room Type" }, { text: "Status" }],
    editedIndex: -1,
    response: "",
    errors: []
  }),

  computed: {},

  watch: {
    // dialog(val) {
    //   val || this.close();
    //   this.errors = [];
    //   this.search = "";
    // }
  },
  created() {
    this.loading = true;
    this.getDataFromApi();
  },

  methods: {
    can(permission) {
      let user = this.$auth;
      return;
      return (
        (user && user.permissions.some(e => e.permission == permission)) ||
        user.master
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
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

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
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
  padding: 7px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
