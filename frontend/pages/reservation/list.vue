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
        <v-autocomplete
          class="form-control"
          @change="getDataFromApi(`reservation`)"
          v-model="pagination.per_page"
          :items="[10, 25, 50, 100]"
          placeholder="Per Page Records"
          solo
          hide-details
          flat
        ></v-autocomplete>
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
      <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
        <span> {{ Model }} List</span>
      </v-toolbar>
      <table>
        <tr>
          <th
            style="font-size:13px"
            v-for="(item, index) in headers"
            :key="index"
          >
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
        <tr style="font-size:13px" v-for="(item, index) in data" :key="index">
          <td class="ps-3">
            <b>{{ item.id }}</b>
          </td>
          <td>{{ item && item.customer.full_name }}</td>
          <td>
            <!-- {{ item.booked_rooms.room_no }} -->
            <span v-for="(room, index) in item.booked_rooms" :key="index">
              {{ room.room_no }}
              {{ item.booked_rooms.length - 1 == index ? "" : "," }}
            </span>
          </td>
          <!-- <td>
            <v-btn
              style="background: linear-gradient(135deg, #23bdb8 0, #65a986 100%) !important;"
              small
              elevation="0"
              dark
              class="l-bg-green-dark"
              v-if="item && item.room.status == 0"
            >
              Available Room
            </v-btn>
            <v-btn
              style="background: linear-gradient(135deg, #f48665 0%, #fda23f 100%) !important;"
              small
              elevation="0"
              dark
              color="l-bg-purple-dark"
              v-else-if="item && item.room.status == 1"
            >
              Booked
            </v-btn>
            <v-btn
              v-else-if="item && item.room.status == 2"
              small
              elevation="0"
              dark
              color="warning"
              >Checked In</v-btn
            >

            <v-btn
              v-else-if="item && item.room.status == 3"
              small
              elevation="0"
              dark
              color="blue"
              >Checked Out</v-btn
            >

            <v-btn
              v-else-if="item && item.room.status == 4"
              small
              elevation="0"
              dark
              >Dirty</v-btn
            >
            <v-btn
              v-else-if="item && item.room.status == 5"
              small
              elevation="0"
              dark
              color="grey"
              >Maintenance</v-btn
            >
          </td> -->
          <td style="width:120px">{{ convert_date_format(item.check_in) }}</td>
          <td style="width:120px">{{ convert_date_format(item.check_out) }}</td>
          <td>{{ item.total_price }}</td>
          <td>{{ item.advance_price }}</td>
          <td>{{ item.remaining_price }}</td>
          <!-- <td>
            <v-btn
              style="background: linear-gradient(135deg, #23bdb8 0, #65a986 100%) !important;"
              small
              elevation="0"
              dark
              class="l-bg-green-dark"
              v-if="item.payment_status == 1"
            >
              Paid
            </v-btn>

            <v-btn
              style="background: linear-gradient(135deg, rgb(243 100 57) 0px, rgb(122 70 67 / 94%) 100%) !important;"
              v-else-if="item.payment_status == 0"
              small
              elevation="0"
              dark
              color="error"
              >Pending</v-btn
            >
          </td> -->
          <td>{{ item.source }}</td>
          <td>{{ item.booking_date }}</td>
          <td>
            <v-icon
              @click="redirect_to_invoice(item.id)"
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
    Model: "Reservation",
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    endpoint: "reservation_list",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "&nbsp RESERVATION NUMBER" },

      { text: "Customer" },
      { text: "Rooms" },
      // { text: "Room Status" },
      { text: "Arrival  Date" },
      { text: "Departure  Date" },
      { text: "Total" },
      { text: "Advance" },
      { text: "Remaining" },
      // { text: "Payment Status" },
      { text: "Source" },
      { text: "Booking Date" },
      { text: "Invoice" }
    ],
    editedIndex: -1,
    response: "",
    errors: []
  }),

  computed: {},

  watch: {
    search() {
      this.getDataFromApi();
      console.log("ff");
    }
  },
  created() {
    // this.loading = true;
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

    redirect_to_invoice(id) {
      let url = process.env.BACKEND_URL + "invoice/" + id;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", url);
      document.body.appendChild(element);
      console.log(element);
      element.click();
    },

    convert_date_format(val) {
      const date = new Date(val);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      console.log(day);
      console.log([year, month, day].join("-"));

      return [year, month, day].join("-");
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
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search
        }
      };

      this.$axios.get(`${url}?  page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        console.log(this.data);
        this.loading = false;
      });
    },

    searchIt() {
      console.log(this.search.length);
      if (this.search.length == 0) {
        this.getDataFromApi();
      } else if (this.search.length > 2) {
        this.getDataFromApi();
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
