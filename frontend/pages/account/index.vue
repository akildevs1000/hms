<template>
  <div v-if="can('management_income_access') && can('management_income_view')">
    <v-row dense>
      <v-col cols="3">
        <v-card class="elevation-2" style="height: 230px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Income
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="CheckOutReportDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              :key="key"
              v-if="income"
              :width="'200px'"
              showPriceFormat="true"
              name="margin"
              size="100%"
              :total="'100'"
              :colors="colors"
              :labels="[
                { color: `#4caf50`, text: `Cash`, value: income.Cash },
                { color: `#538234`, text: `Card`, value: income.Card },
                { color: `#0f642b`, text: `Online`, value: income.Online },
                { color: `#010002`, text: `Bank`, value: income.Bank },
                { color: `#010002`, text: `UPI`, value: income.UPI },
                { color: `#010002`, text: `Cheque`, value: income.Cheque },
                {
                  color: `#010002`,
                  text: `CityLedger`,
                  value: income.CityLedger,
                },
              ]"
            />
            <div v-else>Loading...</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3">
        <v-card class="elevation-2" style="height: 230px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Expenses
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="CheckOutReportDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              :key="key"
              v-if="expense"
              :width="'200px'"
              showPriceFormat="true"
              name="margin"
              size="100%"
              :total="'100'"
              :colors="colors"
              :labels="[
                { color: `#4caf50`, text: `Cash`, value: expense.Cash },
                { color: `#538234`, text: `Card`, value: expense.Card },
                { color: `#0f642b`, text: `Online`, value: expense.Online },
                { color: `#010002`, text: `Bank`, value: expense.Bank },
                { color: `#010002`, text: `UPI`, value: expense.UPI },
                { color: `#010002`, text: `Cheque`, value: expense.Cheque },
              ]"
            />
            <div v-else>Loading...</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3">
        <v-card class="elevation-2" style="height: 230px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Management Expenses
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="CheckOutReportDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              :key="key"
              v-if="managementExpense"
              :width="'200px'"
              showPriceFormat="true"
              name="margin"
              size="100%"
              :total="'100'"
              :colors="colors"
              :labels="[
                {
                  color: `#4caf50`,
                  text: `Cash`,
                  value: managementExpense.Cash,
                },
                {
                  color: `#538234`,
                  text: `Card`,
                  value: managementExpense.Card,
                },
                {
                  color: `#0f642b`,
                  text: `Online`,
                  value: managementExpense.Online,
                },
                {
                  color: `#010002`,
                  text: `Bank`,
                  value: managementExpense.Bank,
                },
                { color: `#010002`, text: `UPI`, value: managementExpense.UPI },
                {
                  color: `#010002`,
                  text: `Cheque`,
                  value: managementExpense.Cheque,
                },
              ]"
            />
            <div v-else>Loading...</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3">
        <v-card class="elevation-2" style="height: 230px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Proffit and Loss
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="CheckOutReportDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              :key="key"
              v-if="income"
              :width="'200px'"
              showPriceFormat="true"
              name="margin"
              size="100%"
              :total="'100'"
              :colors="colors"
              :labels="[
                { color: `#4caf50`, text: `Profit`, value: profit },
                { color: `#538234`, text: `Loss`, value: loss },
                {
                  color: `#0f642b`,
                  text: `City Ledger`,
                  value: income.CityLedger,
                },
              ]"
            />
            <div v-else>Loading...</div>
          </v-card-text>
        </v-card>
      </v-col>
      <!-- <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="green" dark>
          <v-card-title class="white-text"
            >Income <br />

            {{ getPriceFormat(income?.total || 0) }}
          </v-card-title>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="orange" dark>
          <v-card-title
            >Expense <br />
            {{ getPriceFormat(expense?.total || 0) }}
          </v-card-title>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card style="border-radius: 12px" class="pink" dark>
          <v-card-title
            >Management Expense <br />
            {{ getPriceFormat(managementExpense?.total || 0) }}</v-card-title
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
            {{ getPriceFormat(income?.CityLedger || 0) }}</v-card-title
          >
        </v-card>
      </v-col> -->
    </v-row>
    <v-card class="my-2 text-right">
      <v-btn :color="currentTabId === 1 ? `primary` : ''" text @click="currentTabId = 1"
        >Income</v-btn
      >
      <v-btn :color="currentTabId === 2 ? `primary` : ''" text @click="currentTabId = 2">Expense</v-btn>
      <v-btn :color="currentTabId === 3 ? `primary` : ''" text @click="currentTabId = 3">ManagementExpense</v-btn>
      <Income v-show="currentTabId == 1" @stats="(e) => (income = e)" />
      <IncomeExpenseNonManagement v-show="currentTabId == 2" @stats="(e) => (expense = e)" />
      <IncomeExpenseManagement v-show="currentTabId == 3" @stats="(e) => (managementExpense = e)" />
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    currentTabId: 1,
    key: 1,
    colors: [
      "#92d050",
      "#ff0000",
      "#ffc000",
      "#0D652D",
      "#174EA6",
      "#2E3945",
      "#2ECC71",
      "#CE0E2D",
      "#0077B5",
    ],
    Model: "Expense",
    activeTab: 0,
    income: null,
    expense: null,
    managementExpense: null,

    loading: false,
  }),
  created() {
    this.loading = true;
    // this.setCount();
    // setTimeout(() => {
    if (this.income) this.activeTab = 1;
    else
      setTimeout(() => {
        this.activeTab = 0;
      }, 1000);
    // }, 1000);
    // setTimeout(() => {
    if (this.expense) this.activeTab = 2;
    else
      setTimeout(() => {
        this.activeTab = 0;
      }, 2000);
    // }, 2000);
    // setTimeout(() => {
    if (this.managementExpense) this.activeTab = 0;
    else
      setTimeout(() => {
        this.activeTab = 0;
      }, 1000);
    // }, 3000);
  },
  // watch: {
  //   income() {
  //     if (this.income) this.activeTab = 1;
  //   },
  //   expense() {
  //     if (this.expense) this.activeTab = 2;
  //   },
  //   managementExpense() {
  //     if (this.managementExpense) this.activeTab = 0;
  //   },
  //   loss() {
  //     this.key += 1;
  //   },
  //   profit() {
  //     this.key += 1;
  //   },
  // },
  computed: {
    loss() {
      if (this.income && this.expense && this.managementExpense) {
        let combinedExpense = this.expense.total + this.managementExpense.total;

        let result = this.income.total - combinedExpense;

        return result > 0 ? 0 : result;
      } else return 0;
    },

    profit() {
      if (this.income && this.expense && this.managementExpense) {
        let combinedExpense = this.expense.total + this.managementExpense.total;

        let result = this.income.total - combinedExpense;

        return result < 0 ? 0 : result;
      } else return 0;
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
