<template>
  <div v-if="can(`night_audit_access`)">
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
          <FilterDateRange @filter-attr="filterAttr" />
        </v-col>
      </v-row>
    </v-card>

    <v-card class="mt-5 px-2">
      <v-row>
        <v-col md="12" class="text-right">
          <AssetsTable height="500" :headers="headers" :items="items" />
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
    items: [
      {
        date: "2024-10-15",
        checkin: "2024-10-14T14:00",
        checkout: "2024-10-15T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 500,
        income: 1500,
        expense: 200,
        cash_in_hand: 300,
      },
      {
        date: "2024-10-16",
        checkin: "2024-10-15T14:00",
        checkout: "2024-10-16T11:00",
        dayuse: true,
        continue: false,
        closed: true,
        booked: false,
        cancel: true,
        breakfast: false,
        ledger: 300,
        income: 1000,
        expense: 300,
        cash_in_hand: 400,
      },
      {
        date: "2024-10-17",
        checkin: "2024-10-16T14:00",
        checkout: "2024-10-17T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 600,
        income: 2000,
        expense: 500,
        cash_in_hand: 500,
      },
      {
        date: "2024-10-15",
        checkin: "2024-10-14T14:00",
        checkout: "2024-10-15T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 500,
        income: 1500,
        expense: 200,
        cash_in_hand: 300,
      },
      {
        date: "2024-10-16",
        checkin: "2024-10-15T14:00",
        checkout: "2024-10-16T11:00",
        dayuse: true,
        continue: false,
        closed: true,
        booked: false,
        cancel: true,
        breakfast: false,
        ledger: 300,
        income: 1000,
        expense: 300,
        cash_in_hand: 400,
      },
      {
        date: "2024-10-17",
        checkin: "2024-10-16T14:00",
        checkout: "2024-10-17T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 600,
        income: 2000,
        expense: 500,
        cash_in_hand: 500,
      },
      {
        date: "2024-10-15",
        checkin: "2024-10-14T14:00",
        checkout: "2024-10-15T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 500,
        income: 1500,
        expense: 200,
        cash_in_hand: 300,
      },
      {
        date: "2024-10-16",
        checkin: "2024-10-15T14:00",
        checkout: "2024-10-16T11:00",
        dayuse: true,
        continue: false,
        closed: true,
        booked: false,
        cancel: true,
        breakfast: false,
        ledger: 300,
        income: 1000,
        expense: 300,
        cash_in_hand: 400,
      },
      {
        date: "2024-10-17",
        checkin: "2024-10-16T14:00",
        checkout: "2024-10-17T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 600,
        income: 2000,
        expense: 500,
        cash_in_hand: 500,
      },
      {
        date: "2024-10-15",
        checkin: "2024-10-14T14:00",
        checkout: "2024-10-15T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 500,
        income: 1500,
        expense: 200,
        cash_in_hand: 300,
      },
      {
        date: "2024-10-16",
        checkin: "2024-10-15T14:00",
        checkout: "2024-10-16T11:00",
        dayuse: true,
        continue: false,
        closed: true,
        booked: false,
        cancel: true,
        breakfast: false,
        ledger: 300,
        income: 1000,
        expense: 300,
        cash_in_hand: 400,
      },
      {
        date: "2024-10-17",
        checkin: "2024-10-16T14:00",
        checkout: "2024-10-17T11:00",
        dayuse: false,
        continue: true,
        closed: false,
        booked: true,
        cancel: false,
        breakfast: true,
        ledger: 600,
        income: 2000,
        expense: 500,
        cash_in_hand: 500,
      },
    ],
    headers: [
      { align: "center", text: "Date", value: "date" },
      { align: "center", text: "Check In", value: "checkin" },
      { align: "center", text: "Check Out", value: "checkout" },
      { align: "center", text: "Day Use", value: "dayuse" },
      { align: "center", text: "Continue", value: "continue" },
      { align: "center", text: "Closed", value: "closed" },
      { align: "center", text: "Booked", value: "booked" },
      { align: "center", text: "Cancel", value: "cancel" },
      { align: "center", text: "Breakfast", value: "breakfast" },
      { align: "center", text: "Ledger", value: "ledger" },
      { align: "center", text: "Income", value: "income" },
      { align: "center", text: "Expense", value: "expense" },
      { align: "center", text: "Cash In Hand", value: "cash_in_hand" },
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
    let filters = this.$store.getters.getDataToSend;
    if (filters.date) {
      this.from_date = filters.date;
    }

    this.loading = true;
    this.getdata();
  },

  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) {
        this.getdata();
      }
    },
    openExternalLink(path) {
      let url = `${process.env.BACKEND_URL}get_audit_report_print?path=${path}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", url);
      document.body.appendChild(element);
      console.log(element);
      element.click();
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

    getSum(item, type) {
      let sum = 0;
      item.map((e) => {
        e.transactions.map((e) =>
          e.payment_method_id == type ? (sum += parseFloat(e.credit)) : 0
        );
      });
      return sum.toFixed(2);
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

      this.items.map((e) => ({
        date: e.date,
        checkout: e.checkin,
        checkout: e.checkout,
        dayuse: e.dayuse,
        continue: e.continue,
        closed: e.closed,
        booked: e.booked,
        cancel: e.cancel,
        breakfast: e.breakfast,
        ledger: e.ledger,
        income: e.income,
        expense: e.expense,
        cash_in_hand: e.cash_in_hand,
      }));

      return;

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
  },
};
</script>
