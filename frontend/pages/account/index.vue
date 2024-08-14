<template>
  <div v-if="can('management_income_access') && can('management_income_view')">
    <v-row dense>
      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="green" dark>
          <v-card-title class="white-text"
            >Income
            <br />
            {{ getPriceFormat(income.total) }}
          </v-card-title>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="orange" dark>
          <v-card-title
            >Expense <br />
            {{ getPriceFormat(expense.total) }}
          </v-card-title>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="pink" dark>
          <v-card-title
            >Management Expense <br />
            {{ getPriceFormat(managementExpense.total) }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="indigo" dark>
          <v-card-title
            >Profit <br />
            {{ getPriceFormat(profit) }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="brown" dark>
          <v-card-title
            >Loss <br />
            {{ getPriceFormat(loss) }}</v-card-title
          >
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="primary" dark>
          <v-card-title
            >City Ledger <br />
            {{ getPriceFormat(income.CityLedger) }}</v-card-title
          >
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-tabs
            v-model="activeTab"
            background-color="primary"
            dark
            show-arrows
          >
            <v-spacer></v-spacer>
            <v-tab active-class="active-link"> Income List </v-tab>
            <v-tab active-class="active-link"> Expense List </v-tab>
            <v-tab
              active-class="active-link"
              v-if="can('management_income_view')"
            >
              Management Expense List
            </v-tab>

            <v-tabs-slider color="#1259a7"></v-tabs-slider>

            <v-tab-item>
              <Income @stats="(e) => (income = e)" />
            </v-tab-item>

            <v-tab-item>
              <IncomeExpenseNonManagement @stats="(e) => (expense = e)" />
            </v-tab-item>

            <v-tab-item>
              <IncomeExpenseManagement
                @stats="(e) => (managementExpense = e)"
              />
            </v-tab-item>
          </v-tabs>
        </v-card>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    Model: "Expense",
    income: {
      Cash: 0,
      Card: 0,
      Online: 0,
      Bank: 0,
      UPI: 0,
      Cheque: 0,
      CityLedger: 0,
      total: 0,
    },
    expense: {
      Cash: 0,
      Card: 0,
      Online: 0,
      Bank: 0,
      UPI: 0,
      Cheque: 0,
      total: 0,
    },
    managementExpense: {
      Cash: 0,
      Card: 0,
      Online: 0,
      Bank: 0,
      UPI: 0,
      Cheque: 0,
      total: 0,
    },

    loading: false,
  }),
  created() {
    this.loading = true;
    // this.setCount();
  },
  computed: {
    loss() {
      let combinedExpense = this.expense.total + this.managementExpense.total;

      let result = this.income.total - combinedExpense;

      return result > 0 ? 0 : result ;
    },

    profit() {
      let combinedExpense = this.expense.total + this.managementExpense.total;

      let result = this.income.total - combinedExpense;

      return result < 0 ? 0 : result ;
    },
  },
  methods: {
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
  },
};
</script>
