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
    stats: [],

    Model: "Audit Report",
    // from_date: new Date().toJSON().slice(0, 10),
    from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    to_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    from_menu: false,
    options: {},
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
    items: [],
    headers: [],
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

    async getdata() {
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          from_date: this.from_date,
          to_date: this.to_date,
        },
      };

      let { data } = await this.$axios.get("get_audit_report", options);
      this.stats = data.stats;
      this.headers = data.headers;
      this.items = data.data;
    },
  },
};
</script>
