<template>
  <div v-if="can('management_income_access') && can('management_income_view')">
    <v-row dense>
      <v-col cols="12" md="4">
        <v-card
          style="
            border-radius: 12px;
            background: linear-gradient(135deg, #23bdb8, #65a986) !important;
          "
          dark
        >
          <v-card-title
            >Income <br />
            {{ getPriceFormat(counts.income.Total || 0) }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card
          style="
            border-radius: 12px;
            background: linear-gradient(135deg, #f48665, #d68e41) !important;
          "
          dark
        >
          <v-card-title
            >Expense <br />
            {{ getPriceFormat(counts.expense.Total || "0.00") }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px; background: rgb(206, 0, 142)" dark>
          <v-card-title
            >Management Expense <br />
            {{
              getPriceFormat(counts.managementExpense.Total || 0)
            }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card
          style="
            border-radius: 12px;
            background: linear-gradient(135deg, #8e4cf1, #c554bc) !important;
          "
          dark
        >
          <v-card-title
            >Profit <br />
            {{ getPriceFormat(counts.profit || 0) }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card
          style="
            border-radius: 12px;
            background: linear-gradient(135deg,rgb(176, 0, 0) 0px,rgb(176, 0, 0) 100%);
          "
          dark
        >
          <v-card-title
            >Loss <br />
            {{ getPriceFormat(counts.loss || 0) }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px; background: rgb(67, 144, 252)" dark>
          <v-card-title
            >City Ledger <br />
            {{ getPriceFormat(counts.expense.CityLedger || 0) }}</v-card-title
          >
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="4" class="p-2 text-center">
        <CustomFilter @filter-attr="filterAttr" :defaultFilterType="1" />
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-tabs
            v-model="activeTab"
            :vertical="vertical"
            background-color="primary"
            dark
            show-arrows
          >
            <v-spacer></v-spacer>
            <v-tab active-class="active-link"> Income List </v-tab>
            <v-tab active-class="active-link"> {{ Model }} List </v-tab>
            <v-tab
              active-class="active-link"
              v-if="can('management_income_view')"
            >
              Management {{ Model }} List
            </v-tab>

            <v-tabs-slider color="#1259a7"></v-tabs-slider>
            <!-- today checkin -->

            <v-tab-item>
              <Income />
            </v-tab-item>

            <v-tab-item>
              <IncomeExpenseNonManagement />
            </v-tab-item>

            <v-tab-item>
              <IncomeExpenseManagement />
            </v-tab-item>
          </v-tabs>
        </v-card>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
import CustomFilter from "../../components/filter/CustomFilter.vue";

export default {
  data: () => ({
    Model: "Expense",
    vertical: false,
    activeTab: 0,
    from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    from_menu: false,
    to_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    to_menu: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
      status: "-1",
    },
    options: {},
    endpoint: "expense",
    filterType: 1,
    search: "",
    snackbar: false,
    dialog: false,
    counts: {
      expense: {
        Cash: 0,
        Card: 0,
        Online: 0,
        Bank: 0,
        UPI: 0,
        Cheque: 0,
        CityLedger: 0,
        WithOutCityLedger: 0,
        Total: 0,
      },
      managementExpense: {
        Cash: 0,
        Card: 0,
        Online: 0,
        Bank: 0,
        UPI: 0,
        Cheque: 0,
        CityLedger: 0,
        WithOutCityLedger: 0,
        Total: 0,
      },
      income: {
        Cash: 0,
        Card: 0,
        Online: 0,
        Bank: 0,
        UPI: 0,
        Cheque: 0,
        CityLedger: 0,
        WithOutCityLedger: 0,
        Total: 0,
      },
      profit: 0,
      loss: 0,
    },
    loading: false,
  }),
  created() {
    this.loading = true;
    this.get_counts();
  },
  computed: {},
  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) {
        this.get_counts();
      }
    },
    getPriceFormat(price) {
      return (
        this.$auth.user.company.currency +
        " " +
        parseFloat(price).toLocaleString("en-IN", {
          maximumFractionDigits: 2,
        })
      );
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
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
    get_counts() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          from_date: this.from_date,
          to_date: this.to_date,
        },
      };
      this.$axios.get(`account_count`, payload).then(({ data }) => {
        this.counts = data;
      });
    },
  },
  components: { CustomFilter },
};
</script>

<style scoped>
@import url("../../assets/css/tableStyles.css");
</style>
