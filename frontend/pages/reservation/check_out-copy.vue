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

    <v-dialog v-model="payingDialog" persistent max-width="1000px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Payment</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="payingDialog = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-card-text>
          <Paying :BookingData="checkData" @close-dialog="closeDialogs" />
        </v-card-text>
      </v-card>
    </v-dialog>

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
      <v-col xs="12" sm="12" md="3" cols="12">
        <!-- <v-text-field
          class="form-control py-0 custom-text-box floating shadow-none"
          placeholder="Search..."
          solo
          flat
          @input="searchIt"
          v-model="search"
          hide-details
        ></v-text-field> -->
        <v-text-field
          class=""
          label="Search..."
          dense
          outlined
          flat
          append-icon="mdi-magnify"
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
          <th
            style="font-size: 13px"
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
        <tr style="font-size: 13px" v-for="(item, index) in data" :key="index">
          <td class="ps-3">
            <b>{{ item.reservation_no }}</b>
          </td>
          <td>{{ item.source }}</td>
          <td>{{ item.reference_no || "---" }}</td>
          <td>{{ item && item.customer.full_name }}</td>
          <td>
            <span v-for="(room, index) in item.booked_rooms" :key="index">
              {{ room.room_no }}
              {{ item.booked_rooms.length - 1 == index ? "" : "," }}
            </span>
          </td>

          <td style="width: 120px">{{ convert_date_format(item.check_in) }}</td>
          <td style="width: 120px">
            {{ convert_date_format(item.check_out) }}
          </td>
          <td>{{ item.total_price }}</td>
          <td>{{ item.total_posting_amount || 0 }}</td>
          <td>{{ item.paid_amounts || 0 }}</td>
          <td>{{ item.balance || 0 }}</td>

          <td>{{ item.booking_date }}</td>
          <td>
            <!-- v-if="item.payment_status == 1" -->
            <v-btn
              small
              elevation="0"
              dark
              class="l-bg-green-dark"
              :class="getRelaventColor(item.booking_status)"
            >
              {{ getRelaventStatus(item.booking_status) }}
            </v-btn>
          </td>

          <td>
            <v-icon
              @click="viewCustomerBilling(item)"
              x-small
              color="primary"
              class="mr-2"
            >
              mdi-eye
            </v-icon>
          </td>
          <td>
            <v-icon
              @click="get_payment(item)"
              x-small
              color="primary"
              class="mr-2"
            >
              mdi-cash-multiple
            </v-icon>
          </td>
          <td>
            <v-icon
              @click="redirect_to_invoice(item.id)"
              x-small
              color="primary"
              class="mr-2"
            >
              mdi-cash-multiple
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
import Paying from "../../components/booking/Paying.vue";
export default {
  components: {
    Paying,
  },
  data: () => ({
    Model: "Check Out Reservation",
    payingDialog: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 30,
    },
    options: {},
    endpoint: "check_out_reservation_list",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Reser. No" },
      { text: "Source" },
      { text: "Reference" },
      { text: "Customer" },
      { text: "Rooms" },
      { text: "Arrival  Date" },
      { text: "Departure  Date" },
      { text: "Total" },
      { text: "Posting" },
      { text: "Paid Amount" },
      { text: "Balance" },
      { text: "Booking Date" },
      { text: "Reservation Status" },
      { text: "View" },
      { text: "Payment" },
      { text: "Invoice" },
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    checkData: {},
    new_payment: 0,
  }),

  computed: {},

  watch: {
    search() {
      this.getDataFromApi();
    },
  },
  created() {
    this.getDataFromApi();
  },

  methods: {
    can(permission) {
      let user = this.$auth;
      return;
      return (
        (user && user.permissions.some((e) => e.permission == permission)) ||
        user.master
      );
    },

    viewCustomerBilling(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    redirect_to_invoice(id) {
      let url = process.env.BACKEND_URL + "invoice";
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}/${id}`);
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
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },

    closeDialogs() {
      this.payingDialog = false;
    },

    getRelaventColor(status) {
      switch (parseInt(status)) {
        case 1:
          return "booked";
        case 2:
          return "checkedIn";
        default:
          return "checkedOut";
      }
    },

    getRelaventStatus(status) {
      console.log(status);
      switch (parseInt(status)) {
        case 1:
          return "booked";
        case 2:
          return "checkedIn";
        case 3:
          return "checkedOut";
        default:
          return "checkedOut";
      }
    },

    get_payment(item) {
      this.checkData = item;
      this.payingDialog = true;
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;

      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
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
      if (this.search.length == 0) {
        this.getDataFromApi();
      } else if (this.search.length > 2) {
        this.getDataFromApi();
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
  padding: 7px;
  border: 1px solid #e9e9e9;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
