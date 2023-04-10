<template>
  <div>
    <!-- <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Management / {{ Model }}</div>
      </v-col>
    </v-row> -->
    <v-row>
      <div class="col-xl-4 my-0 py-0 col-lg-6 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Checkin</h6>
              <span class="data-1"> RS. {{ GrandTotalCheckin || 0 }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-6 text-uppercase">
        <div class="card px-2" style="background-color: #f29a9a">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Continue</h6>
              <span class="data-1"
                >RS. {{ GrandTotalContinue || 0 }} <br />
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-6 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">CheckOut</h6>
              <span class="data-1">RS. {{ GrandTotalCheckout || 0 }} </span>
            </div>
          </div>
        </div>
      </div>
    </v-row>
    <v-row>
      <v-col md="3">
        <v-menu
          v-model="from_menu"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="from_date"
              readonly
              v-bind="attrs"
              v-on="on"
              dense
              :hide-details="true"
              outlined
              label="Date"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="from_date"
            @input="from_menu = false"
            @change="commonMethod"
          ></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">Today Checkin Report</label>
            <v-spacer></v-spacer>
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  class="ma-0"
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('income_report_print')"
                >
                  <v-icon class="">mdi-printer-outline</v-icon>
                </v-btn>
              </template>
              <span>PRINT</span>
            </v-tooltip>

            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('income_report_download')"
                >
                  <v-icon class="">mdi-download-outline</v-icon>
                </v-btn>
              </template>
              <span> DOWNLOAD </span>
            </v-tooltip>
          </v-toolbar>
          <table>
            <tr>
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in todayCheckIn"
              :key="index"
              style="background-color: yellow"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>

              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">{{ item.advance_price }}</td>

              <td v-for="i in 5" :key="i" class="text-right">
                <span
                  v-if="
                    getPaymentMode(item) == 'Cash' &&
                    item.transactions_sum_credit > 0 &&
                    i == 1
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Card' &&
                    item.transactions_sum_credit > 0 &&
                    i == 2
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Online' &&
                    item.transactions_sum_credit > 0 &&
                    i == 3
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Bank' &&
                    item.transactions_sum_credit > 0 &&
                    i == 4
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'UPI' &&
                    item.transactions_sum_credit > 0 &&
                    i == 5
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span v-else> --- </span>
              </td>
              <td class="text-right">
                {{ item.balance }}
              </td>
              <td>
                {{ item.balance > 0 ? "Payment pending" : "Payment close" }}
              </td>
            </tr>
            <tr class="text-right">
              <th class="text-right" colspan="8">Total</th>
              <th class="text-right">{{ totalCash }}</th>
              <th class="text-right">{{ totalCard }}</th>
              <th class="text-right">{{ totalOnline }}</th>
              <th class="text-right">{{ totalBank }}</th>
              <th class="text-right">{{ totalUPI }}</th>
              <th class="text-right">{{ totalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">Continue Report</label>
            <v-spacer></v-spacer>
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  class="ma-0"
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('income_report_print')"
                >
                  <v-icon class="">mdi-printer-outline</v-icon>
                </v-btn>
              </template>
              <span>PRINT</span>
            </v-tooltip>

            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('income_report_download')"
                >
                  <v-icon class="">mdi-download-outline</v-icon>
                </v-btn>
              </template>
              <span> DOWNLOAD </span>
            </v-tooltip>
          </v-toolbar>
          <table>
            <tr>
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in continueRooms"
              :key="index"
              style="background-color: #9bc1e6"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>

              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">{{ item.advance_price }}</td>

              <td v-for="i in 5" :key="i" class="text-right">
                <span
                  v-if="
                    getPaymentMode(item) == 'Cash' &&
                    item.transactions_sum_credit > 0 &&
                    i == 1
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Card' &&
                    item.transactions_sum_credit > 0 &&
                    i == 2
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Online' &&
                    item.transactions_sum_credit > 0 &&
                    i == 3
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Bank' &&
                    item.transactions_sum_credit > 0 &&
                    i == 4
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'UPI' &&
                    item.transactions_sum_credit > 0 &&
                    i == 5
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span v-else> --- </span>
              </td>
              <td class="text-right">
                {{ item.balance }}
              </td>
              <td>
                {{ item.balance > 0 ? "Payment pending" : "Payment close" }}
              </td>
            </tr>
            <tr class="text-right">
              <th class="text-right" colspan="8">Total</th>
              <th class="text-right">{{ continueTotalCash }}</th>
              <th class="text-right">{{ continueTotalCard }}</th>
              <th class="text-right">{{ continueTotalOnline }}</th>
              <th class="text-right">{{ continueTotalBank }}</th>
              <th class="text-right">{{ continueTotalUPI }}</th>
              <th class="text-right">{{ continueTotalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">CheckOut Report</label>
            <v-spacer></v-spacer>
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  class="ma-0"
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('income_report_print')"
                >
                  <v-icon class="">mdi-printer-outline</v-icon>
                </v-btn>
              </template>
              <span>PRINT</span>
            </v-tooltip>

            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('income_report_download')"
                >
                  <v-icon class="">mdi-download-outline</v-icon>
                </v-btn>
              </template>
              <span> DOWNLOAD </span>
            </v-tooltip>
          </v-toolbar>
          <table>
            <tr>
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in todayCheckOut"
              :key="index"
              style="background-color: #90d24d"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>

              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">{{ item.advance_price }}</td>

              <td v-for="i in 5" :key="i" class="text-right">
                <span
                  v-if="
                    getPaymentMode(item) == 'Cash' &&
                    item.transactions_sum_credit > 0 &&
                    i == 1
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Card' &&
                    item.transactions_sum_credit > 0 &&
                    i == 2
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Online' &&
                    item.transactions_sum_credit > 0 &&
                    i == 3
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'Bank' &&
                    item.transactions_sum_credit > 0 &&
                    i == 4
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span
                  v-else-if="
                    getPaymentMode(item) == 'UPI' &&
                    item.transactions_sum_credit > 0 &&
                    i == 5
                  "
                >
                  {{ item.transactions_sum_credit }}
                </span>
                <span v-else> --- </span>
              </td>
              <td class="text-right">
                {{ item.balance }}
              </td>
              <td>
                {{ item.balance > 0 ? "Payment pending" : "Payment close" }}
              </td>
            </tr>
            <tr class="text-right">
              <th class="text-right" colspan="8">Total</th>
              <th class="text-right">{{ checkoutTotalCash }}</th>
              <th class="text-right">{{ checkoutTotalCard }}</th>
              <th class="text-right">{{ checkoutTotalOnline }}</th>
              <th class="text-right">{{ checkoutTotalBank }}</th>
              <th class="text-right">{{ checkoutTotalUPI }}</th>
              <th class="text-right">{{ checkoutTotalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data: () => ({
    Model: "Audit Report",
    from_date: new Date().toJSON().slice(0, 10),
    from_menu: false,
    options: {},
    endpoint: "expense",
    search: "",
    snackbar: false,
    dialog: false,
    todayCheckIn: [],
    todayCheckOut: [],
    continueRooms: [],
    counts: [],
    loading: false,
    total: 0,

    incomeHeaders: [
      { text: "#" },
      { text: "Guest" },
      { text: "Rooms" },
      { text: "Source" },
      { text: "CheckIn" },
      { text: "CheckOut" },
      { text: "Tariff" },
      { text: "Advance" },
      { text: "Cash" },
      { text: "Card" },
      { text: "Online" },
      { text: "Bank" },
      { text: "UPI" },
      { text: "Balance" },
      { text: "Remark" },
    ],
    response: "",
    loss: "",
    profit: "",
    errors: [],
    editedItem: {
      item: null,
      amount: null,
      payment_modes: "CASH",
    },
    totalExpenses: {},
  }),

  created() {
    this.loading = true;
    this.getdata();
  },

  computed: {
    totalBalance() {
      let sum = 0;
      this.todayCheckIn.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    totalCash() {
      return this.getSum(this.todayCheckIn, 1);
    },
    totalCard() {
      return this.getSum(this.todayCheckIn, 2);
    },
    totalBank() {
      return this.getSum(this.todayCheckIn, 4);
    },
    totalOnline() {
      return this.getSum(this.todayCheckIn, 3);
    },
    totalUPI() {
      return this.getSum(this.todayCheckIn, 5);
    },

    continueTotalBalance() {
      let sum = 0;
      this.continueRooms.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    continueTotalCash() {
      return this.getSum(this.continueRooms, 1);
    },
    continueTotalCard() {
      return this.getSum(this.continueRooms, 2);
    },
    continueTotalBank() {
      return this.getSum(this.continueRooms, 4);
    },
    continueTotalOnline() {
      return this.getSum(this.continueRooms, 3);
    },
    continueTotalUPI() {
      return this.getSum(this.continueRooms, 5);
    },

    checkoutTotalBalance() {
      let sum = 0;
      this.todayCheckOut.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    checkoutTotalCash() {
      return this.getSum(this.todayCheckOut, 1);
    },
    checkoutTotalCard() {
      return this.getSum(this.todayCheckOut, 2);
    },
    checkoutTotalBank() {
      return this.getSum(this.todayCheckOut, 4);
    },
    checkoutTotalOnline() {
      return this.getSum(this.todayCheckOut, 3);
    },
    checkoutTotalUPI() {
      return this.getSum(this.todayCheckOut, 5);
    },

    GrandTotalCheckin() {
      let tot =
        parseFloat(this.totalCash) +
        parseFloat(this.totalCard) +
        parseFloat(this.totalBank) +
        parseFloat(this.totalOnline) +
        parseFloat(this.totalUPI);
      return tot.toFixed(2);
    },

    GrandTotalCheckout() {
      let tot =
        parseFloat(this.checkoutTotalCash) +
        parseFloat(this.checkoutTotalCard) +
        parseFloat(this.checkoutTotalBank) +
        parseFloat(this.checkoutTotalOnline) +
        parseFloat(this.checkoutTotalUPI);
      return tot.toFixed(2);
    },

    GrandTotalContinue() {
      let tot =
        parseFloat(this.continueTotalCash) +
        parseFloat(this.continueTotalCard) +
        parseFloat(this.continueTotalBank) +
        parseFloat(this.continueTotalOnline) +
        parseFloat(this.continueTotalUPI);
      return tot.toFixed(2);
    },
  },

  methods: {
    onPageChange() {
      this.getExpenseData();
    },
    can(permission) {
      return this.$auth.user.user_type == "company" ? true : false;
      return (
        (user && user.permissions.some((e) => e.permission == permission)) ||
        user.master
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    getSum(item, type) {
      let sum = 0;
      item.map((e) => {
        e.transactions.map((e) =>
          e.payment_method_id == type ? (sum += parseFloat(e.credit)) : 0
        );
      });
      return sum.toFixed(2);
    },

    process(type) {
      alert("coming soon, developing");
      return;
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },

    getPaymentMode(item) {
      const lastTransaction = item.transactions[item.transactions.length - 1];
      let creditTrans = item.transactions.filter((e) => e.credit > 0);
      let lastcredit = creditTrans[creditTrans.length - 1];
      console.log(lastcredit);
      if (lastcredit != undefined) {
        return lastcredit.payment_mode.name;
      }
    },

    commonMethod() {
      this.getdata();
    },

    getdata(url = "get_audit_report") {
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          // date: "2023-04-10",
          date: this.from_date,
        },
      };
      this.$axios.get(url, options).then(({ data }) => {
        this.todayCheckIn = data.todayCheckIn;
        this.continueRooms = data.continueRooms;
        this.todayCheckOut = data.todayCheckOut;
      });
    },
  },
};
</script>

<style scoped src="@/assets/dashtem.css"></style>
<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 5px;
  font-size: 13px;
}

table,
th,
td {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
}

.dashboard-payment-card {
  margin: 10px;
  background-color: #fff;
  border-radius: 5px;
  padding: 5px 20px;
}
</style>
