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

    <v-dialog v-model="checkOutDialog" persistent max-width="1000px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Payment</span>
        </v-toolbar>
        <v-card-text>
          <v-row>
            <v-col md="7">
              <v-container>
                <table>
                  <v-progress-linear
                    v-if="false"
                    :active="loading"
                    :indeterminate="loading"
                    absolute
                    color="primary"
                  ></v-progress-linear>
                  <tr>
                    <th>Customer Name</th>
                    <td style="width: 300px">
                      {{ checkData && checkData.title }}
                    </td>
                  </tr>
                  <tr>
                    <th>Room No</th>
                    <td>
                      {{ checkData.rooms }}
                    </td>
                  </tr>
                  <tr>
                    <th>Check In</th>
                    <td>
                      {{ checkData && checkData.check_in_date }}
                    </td>
                  </tr>
                  <tr>
                    <th>Check Out</th>
                    <td>
                      {{ checkData && checkData.check_out_date }}
                    </td>
                  </tr>
                  <tr>
                    <th>
                      Payment Mode
                      <span class="text-danger">*</span>
                    </th>
                    <td>
                      <v-select
                        v-model="checkData.payment_mode_id"
                        :items="[
                          { id: 1, name: 'Cash' },
                          { id: 2, name: 'Card' },
                          { id: 3, name: 'Online' },
                          { id: 4, name: 'Bank' },
                          { id: 5, name: 'UPI' },
                          { id: 6, name: 'Cheque' },
                          { id: 7, name: 'City Ledger' },
                        ]"
                        item-text="name"
                        item-value="id"
                        dense
                        outlined
                        :hide-details="true"
                        :height="1"
                      ></v-select>
                    </td>
                  </tr>
                  <tr>
                    <th>Total Amount</th>
                    <td>{{ checkData && checkData.total_price }}</td>
                  </tr>
                  <tr>
                    <th>Total Posting Amount</th>
                    <td>{{ checkData && checkData.total_posting_amount }}</td>
                  </tr>
                  <tr>
                    <th>Remaining Balance</th>
                    <td>{{ checkData.remaining_price }}</td>
                  </tr>
                  <tr>
                    <th>Remaining Balance With Posting</th>
                    <td>{{ checkData.grand_remaining_price }}</td>
                  </tr>
                  <tr style="background-color: white">
                    <th>
                      Payment
                      <span class="text-danger">*</span>
                    </th>
                    <td>
                      <v-text-field
                        dense
                        outlined
                        type="number"
                        v-model="new_payment"
                        :hide-details="true"
                      ></v-text-field>
                    </td>
                  </tr>
                  <tr></tr>
                </table>
              </v-container>
            </v-col>
            <v-col md="5" class="mt-3">
              <table>
                <tr
                  style="font-size: 13px; background-color: white; color: black"
                >
                  <th>#</th>
                  <th>Date</th>
                  <th>Debit</th>
                  <th>Credit</th>
                  <th>Balance</th>
                </tr>

                <tr
                  v-for="(item, index) in transactions"
                  :key="index"
                  style="font-size: 13px; background-color: white; color: black"
                >
                  <td>
                    <b>{{ ++index }}</b>
                  </td>
                  <td>{{ item.created_at || "---" }}</td>
                  <td class="text-right">
                    {{ item && item.debit == 0 ? "---" : item.debit }}
                  </td>
                  <td class="text-right">
                    {{ item && item.credit == 0 ? "---" : item.credit }}
                  </td>
                  <td class="text-right">{{ item.balance || "---" }}</td>
                </tr>
                <tr
                  style="font-size: 13px; background-color: white; color: black"
                >
                  <th colspan="4" class="text-right">Balance</th>
                  <td class="text-right" style="background-color: white">
                    {{ totalTransactionAmount }}
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn class="primary" small @click="pay_amount">Pay</v-btn>
          <v-btn class="error" small @click="checkOutDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
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
            <b>{{ item.show_taxable_invoice_number || "---" }}</b>
          </td>
          <td class="ps-3">
            <b>{{ item.reservation_no || "---" }}</b>
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
          <!-- <td>
            <v-btn
              small
              elevation="0"
              dark
              class="l-bg-green-dark"
              :class="getRelaventColor(item.booking.booking_status)"
            >
              {{ getRelaventStatus(item.booking.booking_status) }}
            </v-btn>
          </td> -->

          <!-- <td>
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
    Model: "GST Bills",
    checkOutDialog: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 30,
    },
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
      { text: "&nbsp Reservation No" },
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
      // { text: "Reservation Status" }
      // { text: "View" },
      // { text: "Payment" },
      // { text: "Invoice" }
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    checkData: {},
    transactions: [],
    totalTransactionAmount: 0,
    new_payment: 0,
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
      this.checkOutDialog = true;
      this.get_transaction(item.id);
    },

    get_transaction(bookingId) {
      let id = bookingId;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios
        .get(`get_transaction_by_booking_id/${id}`, payload)
        .then(({ data }) => {
          this.transactions = data.data;
          this.transactions = data.transactions;
          this.totalTransactionAmount = data.totalTransactionAmount;
        });
    },

    pay_amount() {
      if (this.new_payment == "") {
        alert("Enter amount");
        return;
      }
      // this.loading = true;
      let payload = {
        new_advance: this.new_payment,
        booking_id: this.checkData.id,
        grand_remaining_price: this.checkData.grand_remaining_price,
        remaining_price: this.checkData.remaining_price,
        payment_mode_id: this.checkData.payment_mode_id,
        company_id: this.$auth.user.company.id,
      };
      return;
      this.$axios
        .post("/paying_amount", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            // this.succuss(data, false, false, false, true);
            this.get_transaction(data.bookingId);
          }
        })
        .catch((e) => console.log(e));
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
        console.log(this.data);
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
