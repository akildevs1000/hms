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
        <v-text-field placeholder="Search..." @input="searchIt" v-model="search" hide-details label="Search..." dense
          outlined flat append-icon="mdi-magnify"></v-text-field>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="3">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
              class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense class="custom-text-box shadow-none"
              solo flat label="To" :hide-details="true"></v-text-field>
          </template>
          <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> {{ Model }} List</span>
        <v-spacer></v-spacer>

        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
              @click="process('gst_invoice_report_print')">
              <v-icon class="">mdi-printer-outline</v-icon>
            </v-btn>
          </template>
          <span>PRINT</span>
        </v-tooltip>

        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="process('gst_invoice_report_download')">
              <v-icon class="">mdi-download-outline</v-icon>
            </v-btn>
          </template>
          <span> DOWNLOAD </span>
        </v-tooltip>
      </v-toolbar>
      <table>
        <tr>
          <th style="font-size: 13px" v-for="(item, index) in headers" :key="index">
            <span v-html="item.text"></span>
          </th>
        </tr>
        <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
          color="primary"></v-progress-linear>
        <tr style="font-size: 13px" v-for="(item, index) in data" :key="index">
          <td class="ps-3">
            <b>{{ item.show_taxable_invoice_number || "---" }}</b>
          </td>
          <td class="ps-3">
            <b>

              <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer;">
                {{ item.reservation_no || "---" }}
              </span>

            </b>
          </td>
          <td>
            {{
              (item &&
                item.booking &&
                item.booking.customer &&
                item.booking.customer.gst_number) ||
              "---"
            }}
          </td>
          <td>{{ (item && item.booking && item.booking.source) || "---" }}</td>
          <td>
            {{
              (item &&
                item.booking &&
                item.booking.customer &&
                item.booking.customer.full_name) ||
              "---"
            }}
          </td>

          <td style="width: 120px">
            {{
              convert_date_format(item && item.booking && item.booking.check_in)
            }}
          </td>
          <td style="width: 120px">
            {{
              convert_date_format(
                item && item.booking && item.booking.check_out
              )
            }}
          </td>
          <td>
            {{ (item && item.booking && item.booking.total_price) || "0" }}
          </td>
          <td>
            {{
              (item && item.booking && item.booking.total_posting_amount) || 0
            }}
          </td>
          <td>
            {{ (item && item.booking && item.booking.paid_amounts) || 0 }}
          </td>
          <td>{{ (item && item.booking && item.booking.balance) || 0 }}</td>

          <td>
            {{ (item && item.booking && item.booking.booking_date) || "---" }}
          </td>
          <td>
            <v-icon @click="
              redirect_to_invoice(
                item && item.booking && item.booking.id,
                item.show_taxable_invoice_number
              )
            " x-small color="primary" class="mr-2">
              mdi-cash-multiple
            </v-icon>
          </td>
        </tr>
      </table>
    </v-card>
    <v-row>
      <v-col md="12" class="float-right">
        <div class="float-right">
          <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
            :total-visible="12"></v-pagination>
        </div>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    Model: "GST Bill",
    checkOutDialog: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 30,
    },

    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,

    options: {},
    endpoint: "taxable_invoice",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "&nbsp Invoice No" },
      { text: "&nbsp Resr. No" },
      { text: "&nbsp GST" },
      { text: "Source" },
      { text: "Customer" },
      // { text: "Rooms" },
      { text: "Arrival  Date" },
      { text: "Departure  Date" },
      { text: "Total" },
      { text: "Posting" },
      { text: "Paid Amount" },
      { text: "Balance" },
      { text: "Booking Date" },
      { text: "Invoice" },
      // { text: "Reservation Status" }
      // { text: "View" },
      // { text: "Payment" },
      // { text: "Invoice" }
    ],
    editedIndex: -1,
    response: "",
    errors: [],
  }),

  computed: {},

  watch: {
    search() {
      this.getDataFromApi();
    },
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
        (user && user.permissions.some((e) => e.permission == permission)) ||
        user.master
      );
    },

    convert_date_format(val) {
      if (!val) return "---";
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

    commonMethod() {
      this.getDataFromApi();
    },

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.booking_id}`);
    },

    redirect_to_invoice(id, inv) {
      let url = process.env.BACKEND_URL + "invoice";
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}/${id}/${inv}`);
      document.body.appendChild(element);
      element.click();
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;

      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
          from: this.from_date,
          to: this.to_date,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
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
