<template>
  <div v-if="can(`night_audit_access`)">
    <style scoped>
      td,
      th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
      }
    </style>
    <v-card class="px-2">
      <v-row>
        <v-col v-for="(stat, index) in stats" :key="index">
          <AssetsCard :key="index" :options="stat" />
        </v-col>
      </v-row>
    </v-card>
    <v-card class="mt-5 px-2">
      <v-row class="text-left">
        <v-col cols="10"></v-col>
        <v-col cols="2">
          <v-menu
            v-model="from_menu"
            :close-on-content-click="false"
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
              no-title
              v-model="from_date"
              @input="from_menu = false"
              @change="commonMethod"
            ></v-date-picker>
          </v-menu>
        </v-col>
      </v-row>
    </v-card>

    <v-card class="mt-5 px-2">
      <v-row>
        <v-col md="12" class="text-right">
          <AssetsTable
            :headers="[
              { text: `#`, value: `serial`, align: `center` },
              { text: `Sent To`, value: `sent_to`, align: `center` },
              { text: `Date Time`, value: `created_at`, align: `center` },
              { text: `File`, value: `file`, align: `center` },
            ]"
            :items="[
              {
                serial: 1,
                file: 1,
                sent_to: `demo@gmail.com`,
                created_at: new Date(),
              },
              {
                serial: 2,
                file: 2,
                sent_to: `demo@gmail.com`,
                created_at: new Date(),
              },
              {
                serial: 3,
                file: 3,
                sent_to: `demo@gmail.com`,
                created_at: new Date(),
              },
              {
                serial: 4,
                file: 4,
                sent_to: `demo@gmail.com`,
                created_at: new Date(),
              },
            ]"
          />
        </v-col>
      </v-row>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
import CheckinAudit from "../../../components/audit/CheckinAudit.vue";
export default {
  components: {
    CheckinAudit,
  },
  data: () => ({
    stats: [
      {
        icon: "mdi-door-open", // Represents Checkin
        value: 11,
        label: "Checkin",
        col: 7,
        color: "green", // For activity (Walking)
      },
      {
        icon: "mdi-clock-outline", // Represents Continue (ongoing activity)
        value: 11,
        label: "Continue",
        col: 7,
        color: "blue", // For online/technology (OTA)
      },
      {
        icon: "mdi-door-closed", // Represents CheckOut
        value: 11,
        label: "CheckOut",
        col: 7,
        color: "orange", // For business (Corporate)
      },
      {
        icon: "mdi-calendar-check", // Represents Booking
        value: 11,
        label: "Booking",
        col: 7,
        color: "purple", // For cloud/web (Website)
      },
      {
        icon: "mdi-cash-multiple", // Represents City Ledger (accounts, payments)
        value: 11,
        label: "City Ledger",
        col: 7,
        color: "pink", // For gifts (Complimentary)
      },
      {
        icon: "mdi-cancel", // Represents Cancel Rooms
        value: 11,
        label: "Cancel Rooms",
        col: 7,
        color: "grey", // For service/people (Travel Agent)
      },
      {
        icon: "mdi-silverware-fork-knife", // Represents Food Order
        value: 11,
        label: "Food Order",
        col: 7,
        color: "teal", // For service/people (Travel Agent)
      },
    ],

    Model: "Audit Report",
    // from_date: new Date().toJSON().slice(0, 10),
    from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    from_menu: false,
    options: {},
    endpoint: "expense",
    search: "",
    snackbar: false,
    dialog: false,
    todayCheckIn: [],
    todayCheckOut: [],
    continueRooms: [],
    todayPayments: [],
    cityLedgerPaymentsAudit: [],
    cancelRooms: [],

    checkInFileGenerateDateTime: null,
    checkInFilePath: null,

    continueRoomsFileGenerateDateTime: null,
    continueRoomsFilePath: null,

    todayCheckOutGenerateDateTime: null,
    todayCheckOutPath: null,

    todayPaymentsGenerateDateTime: null,
    todayPaymentsPath: null,

    cityLedgerGenerateDateTime: null,
    cityLedgerPath: null,

    cancelRoomsGenerateDateTime: null,
    cancelRoomsPath: null,

    foodGenerateDateTime: null,
    foodPath: null,

    counts: [],
    loading: false,
    total: 0,
    totExpense: 0,

    vertical: false,
    activeTab: 0,

    headers: [
      { align: "left", text: "#" },
      { align: "left", text: "Guest" },
      { align: "left", text: "Rev. No" },
      { align: "left", text: "Rooms" },
      { align: "left", text: "Source" },
      { align: "left", text: "CheckIn" },
      { align: "left", text: "CheckOut" },
      { align: "left", text: "Tariff" },
      { align: "left", text: "Advance" },
      { align: "left", text: "Cash" },
      { align: "left", text: "Card" },
      { align: "left", text: "Online" },
      { align: "left", text: "Bank" },
      { align: "left", text: "UPI" },
      { align: "left", text: "Balance" },
      { align: "left", text: "Remark" },
      { align: "center", text: "File Generated Date Time" },
      { align: "center", text: "PDF File" },
    ],
    response: "",
    loss: "",
    profit: "",
    errors: [],
    FoodData: [],
    editedItem: {
      item: null,
      amount: null,
      payment_modes: "CASH",
    },
    totalExpenses: {},
  }),

  created() {
    let filters = this.$store.getters.getDataToSend;
    if (filters.date) {
      this.from_date = filters.date;
    }

    this.loading = true;
    this.getdata();
    this.get_food_order_list();
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

    todayPaymentTotalBalance() {
      let sum = 0;
      this.todayPayments.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    todayPaymentTotalCash() {
      return this.getSum(this.todayPayments, 1);
    },
    todayPaymentTotalCard() {
      return this.getSum(this.todayPayments, 2);
    },
    todayPaymentTotalBank() {
      return this.getSum(this.todayPayments, 4);
    },
    todayPaymentTotalOnline() {
      return this.getSum(this.todayPayments, 3);
    },
    todayPaymentTotalUPI() {
      return this.getSum(this.todayPayments, 5);
    },

    cityLedgerTotalBalance() {
      let sum = 0;
      this.cityLedgerPaymentsAudit.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    cityLedgerTotalCash() {
      return this.getSum(this.cityLedgerPaymentsAudit, 1);
    },
    cityLedgerTotalCard() {
      return this.getSum(this.cityLedgerPaymentsAudit, 2);
    },
    cityLedgerTotalBank() {
      return this.getSum(this.cityLedgerPaymentsAudit, 4);
    },
    cityLedgerTotalOnline() {
      return this.getSum(this.cityLedgerPaymentsAudit, 3);
    },
    cityLedgerTotalUPI() {
      return this.getSum(this.cityLedgerPaymentsAudit, 5);
    },

    GrandTotal() {
      let tot =
        parseFloat(this.GrandTotalCash) +
        parseFloat(this.GrandTotalCard) +
        parseFloat(this.GrandTotalBank) +
        parseFloat(this.GrandTotalTodayOnline) +
        parseFloat(this.GrandTotalTodayUPI);
      return tot.toFixed(2);
    },

    GrandTotalCash() {
      let tot =
        parseFloat(this.cityLedgerTotalCash) +
        parseFloat(this.totalCash) +
        // parseFloat(this.totalUPI) +
        parseFloat(this.checkoutTotalCash) +
        parseFloat(this.continueTotalCash) +
        parseFloat(this.todayPaymentTotalCash);
      return tot.toFixed(2);
    },

    GrandTotalCard() {
      let tot =
        parseFloat(this.totalCard) +
        parseFloat(this.checkoutTotalCard) +
        parseFloat(this.continueTotalCard) +
        parseFloat(this.todayPaymentTotalCard);
      parseFloat(this.cityLedgerTotalCard);
      return tot.toFixed(2);
    },

    GrandTotalBank() {
      let tot =
        parseFloat(this.continueTotalBank) +
        parseFloat(this.todayPaymentTotalBank) +
        parseFloat(this.checkoutTotalBank) +
        parseFloat(this.cityLedgerTotalBank) +
        parseFloat(this.totalBank);
      return tot.toFixed(2);
    },

    GrandTotalTodayOnline() {
      let tot =
        parseFloat(this.continueTotalOnline) +
        parseFloat(this.todayPaymentTotalOnline) +
        parseFloat(this.checkoutTotalOnline) +
        parseFloat(this.cityLedgerTotalOnline) +
        parseFloat(this.totalOnline);
      return tot.toFixed(2);
    },

    GrandTotalTodayUPI() {
      let tot =
        parseFloat(this.continueTotalUPI) +
        parseFloat(this.todayPaymentTotalUPI) +
        parseFloat(this.checkoutTotalUPI) +
        parseFloat(this.cityLedgerTotalUPI) +
        parseFloat(this.totalUPI);
      return tot.toFixed(2);
    },

    GrandTotalBalance() {
      let tot =
        parseFloat(this.totalBalance) +
        parseFloat(this.continueTotalBalance) +
        parseFloat(this.checkoutTotalBalance) +
        parseFloat(this.cityLedgerTotalBalance) +
        parseFloat(this.todayPaymentTotalBalance);
      return tot.toFixed(2);
    },
  },

  methods: {
    openExternalLink(path) {
      let url = `${process.env.BACKEND_URL}get_audit_report_print?path=${path}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", url);
      document.body.appendChild(element);
      console.log(element);
      element.click();
    },
    onPageChange() {
      this.getExpenseData();
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
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

    goToRevViewFromCancel(item) {
      this.$router.push(`/customer/details/${item.booking_id}`);
    },

    getTimeFromCheckIn(date) {
      const dateObj = new Date(date);
      const hours = dateObj.getHours().toString().padStart(2, "0");
      const minutes = dateObj.getMinutes().toString().padStart(2, "0");
      return `${hours}:${minutes}`;
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

    setAdvancePayment(amt) {
      return amt > 0 ? amt : 0;
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

    getPaymentMode(item, mode) {
      // let creditTrans = item.transactions.filter((e) => e.credit > 0);
      let creditTrans = item.transactions;
      switch (mode) {
        case 1:
          return this.getPaySum(creditTrans, 1);
        case 2:
          return this.getPaySum(creditTrans, 2);
        case 3:
          return this.getPaySum(creditTrans, 3);
        case 4:
          return this.getPaySum(creditTrans, 4);
        case 5:
          return this.getPaySum(creditTrans, 5);
        default:
          break;
      }
    },

    getPaySum(payload, mode) {
      let sum = 0;
      payload.map((e) => {
        if (e.payment_method_id == mode) {
          sum += parseFloat(e.credit);
        }
      });
      return sum.toFixed(2);
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
        this.todayCheckIn = data.check_in.data;
        this.continueRooms = data.continue.data;
        this.todayCheckOut = data.check_out.data;
        this.todayPayments = data.payment.data;
        this.cityLedgerPaymentsAudit = data.cityLedger.data;
        this.cancelRooms = data.cancel.data;

        this.checkInFileGenerateDateTime = data.check_in.dateTime;
        this.checkInFilePath = data.check_in.file_path;

        this.continueRoomsFileGenerateDateTime = data.continue.dateTime;
        this.continueRoomsFilePath = data.continue.file_path;

        this.todayCheckOutGenerateDateTime = data.check_out.dateTime;
        this.todayCheckOutPath = data.check_out.file_path;

        this.todayPaymentsGenerateDateTime = data.payment.dateTime;
        this.todayPaymentsPath = data.payment.file_path;

        this.cityLedgerGenerateDateTime = data.cityLedger.dateTime;
        this.cityLedgerPath = data.cityLedger.file_path;

        this.cancelRoomsGenerateDateTime = data.cancel.dateTime;
        this.cancelRoomsPath = data.cancel.file_path;

        this.foodGenerateDateTime = data.food.dateTime;
        this.foodPath = data.food.file_path;

        this.totExpense = data.expense.data;
      });
    },

    get_food_order_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`food/`, payload).then(({ data }) => {
        this.FoodData = data;
      });
    },
  },
};
</script>
