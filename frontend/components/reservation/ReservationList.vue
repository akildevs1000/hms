<template>
  <div>
    <v-row>
      <!-- <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="form-control"
          @change="getDataFromApi(`reservation`)"
          v-model="pagination.per_page"
          :items="[10, 25, 50, 100]"
          placeholder="Per Page Records"
          solo
          hide-details
          flat
        ></v-select>
      </v-col> -->

      <v-col xs="12" sm="12" md="3" cols="12" class="mt-0">
        <v-select
          class="form-control"
          @change="getDataFromApi(`reservation_list_dash`)"
          v-model="status"
          :items="reservationStatusList"
          item-text="name"
          item-value="id"
          placeholder="Display List"
          solo
          hide-details
          flat
          dense
        ></v-select>
      </v-col>
    </v-row>
    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
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
            <span v-for="(room, index) in item.booked_rooms" :key="index">
              {{ room.room_no }}
              {{ item.booked_rooms.length - 1 == index ? "" : "," }}
            </span>
          </td>

          <td style="width:120px">{{ convert_date_format(item.check_in) }}</td>
          <td style="width:120px">{{ convert_date_format(item.check_out) }}</td>
          <td>{{ item.total_price }}</td>
          <td>{{ item.advance_price }}</td>
          <td>{{ item.remaining_price }}</td>

          <td>{{ item.source }}</td>
          <td>{{ convert_date_format(item.booking_date) }}</td>
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
    endpoint: "reservation_list_dash",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    reservationStatusList: [
      { id: "", name: "Summary" },
      { id: 1, name: "Arrival For Today" },
      { id: 2, name: "Occupancy" },
      { id: 3, name: "Departure For Today" }
    ],
    headers: [
      { text: "&nbsp RESERVATION NUMBER" },
      { text: "Customer" },
      { text: "Rooms" },
      { text: "Arrival  Date" },
      { text: "Departure  Date" },
      { text: "Total" },
      { text: "Advance" },
      { text: "Remaining" },
      { text: "Source" },
      { text: "Booking Date" }
    ],
    editedIndex: -1,
    response: "",
    status: "",
    errors: []
  }),

  computed: {},

  watch: {
    search() {
      this.getDataFromApi();
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

    viewCustomerBilling(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `http://127.0.0.1:8000/api/invoice/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    convert_date_format(val) {
      const date = new Date(val);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
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
          status: this.status,
          date: new Date().toJSON().slice(0, 10)
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
