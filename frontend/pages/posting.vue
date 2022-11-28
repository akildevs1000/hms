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
          @change="getDataFromApi(`posting`)"
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
          @change="getDataFromApi(`posting`)"
          v-model="pagination.room_no"
          :items="rooms"
          item-text="room_no"
          item-value="id"
          placeholder="Room No"
          solo
          hide-details
          flat
        ></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-text-field
          class="form-control py-0 custom-text-box floating shadow-none"
          placeholder="Bill No..."
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
          <td>{{ ++index }}</td>
          <td>{{ caps(item.bill_no) }}</td>
          <td>{{ caps(item.booking && item.booking.id) }}</td>
          <td>{{ caps(item.booked_room && item.booked_room.room_no) }}</td>
          <td>{{ caps(item.booked_room && item.booked_room.room_type) }}</td>
          <td>{{ caps(item.booking.customer.full_name) }}</td>

          <td>{{ caps(item.item) }}</td>
          <td>{{ caps(item.qty) }}</td>
          <td>{{ caps(item.amount_with_tax) }}</td>
          <td>{{ caps(item.tax) }}</td>
          <td>{{ caps(item.sgst) }}</td>
          <td>{{ caps(item.cgst) }}</td>
          <td>{{ caps(item.posting_date) }}</td>
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
    Model: "Posting",
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
      room_no: ""
    },
    options: {},
    endpoint: "posting",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "#" },
      { text: "Bill Number" },
      { text: "Reservation Number" },
      { text: "Room No" },
      { text: "Room Type" },
      { text: "Customer" },
      { text: "Item" },
      { text: "QTY" },
      { text: "Amount" },
      { text: "Tax" },
      { text: "SGST" },
      { text: "CGST" },
      { text: "Date" }
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    rooms: []
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
    this.room_list();
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

    room_list() {
      this.$axios.get(`room_list_menu`).then(({ data }) => {
        this.rooms = data;
        this.rooms.unshift({ id: "", room_no: "Select All" });
      });
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          room_id: this.pagination.room_no,
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
      } else if (e.length > 1) {
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
