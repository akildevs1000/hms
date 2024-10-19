<template>
  <div v-if="can('management_income_access') && can('management_income_view')">
    <v-dialog v-model="IncomeCardDialog" persistent max-width="700px">
      <AssetsIconClose left="690" @click="IncomeCardDialog = false" />
      <v-card>
        <v-alert class="grey lighten-3" dense>
          <span class="text-color">Income</span>
        </v-alert>
        <v-card-text>
          <v-container>
            <AssetsTable
              v-if="income"
              :headers="[
                { text: `Cash`, value: `Cash`, align: `center` },
                { text: `Card`, value: `Card`, align: `center` },
                { text: `Online`, value: `Online`, align: `center` },
                { text: `Bank`, value: `Bank`, align: `center` },
                { text: `UPI`, value: `UPI`, align: `center` },
                { text: `Cheque`, value: `Cheque`, align: `center` },
                { text: `CityLedger`, value: `CityLedger`, align: `center` },
              ]"
              :items="[
                {
                  Cash: $utils.currency_format(income.Cash),
                  Card: $utils.currency_format(income.Card),
                  Online: $utils.currency_format(income.Online),
                  Bank: $utils.currency_format(income.Bank),
                  UPI: $utils.currency_format(income.UPI),
                  Cheque: $utils.currency_format(income.Cheque),
                  CityLedger: $utils.currency_format(income.CityLedger),
                },
              ]"
            />
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="ExpenseCardDialog" persistent max-width="700px">
      <AssetsIconClose left="690" @click="ExpenseCardDialog = false" />
      <v-card>
        <v-alert class="grey lighten-3" dense>
          <span class="text-color">Expense</span>
        </v-alert>
        <v-card-text>
          <v-container>
            <AssetsTable
              v-if="expense"
              :headers="[
                { text: `Cash`, value: `Cash`, align: `center` },
                { text: `Card`, value: `Card`, align: `center` },
                { text: `Online`, value: `Online`, align: `center` },
                { text: `Bank`, value: `Bank`, align: `center` },
                { text: `UPI`, value: `UPI`, align: `center` },
                { text: `Cheque`, value: `Cheque`, align: `center` },
                { text: `CityLedger`, value: `CityLedger`, align: `center` },
              ]"
              :items="[
                {
                  Cash: $utils.currency_format(expense.Cash),
                  Card: $utils.currency_format(expense.Card),
                  Online: $utils.currency_format(expense.Online),
                  Bank: $utils.currency_format(expense.Bank),
                  UPI: $utils.currency_format(expense.UPI),
                  Cheque: $utils.currency_format(expense.Cheque),
                  CityLedger: $utils.currency_format(expense.CityLedger),
                },
              ]"
            />
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="ManagementCardDialog" persistent max-width="700px">
      <AssetsIconClose left="690" @click="ManagementCardDialog = false" />
      <v-card>
        <v-alert class="grey lighten-3" dense>
          <span class="text-color">Management Expense</span>
        </v-alert>
        <v-card-text>
          <v-container>
            <AssetsTable
              v-if="managementExpense"
              :headers="[
                { text: `Cash`, value: `Cash`, align: `center` },
                { text: `Card`, value: `Card`, align: `center` },
                { text: `Online`, value: `Online`, align: `center` },
                { text: `Bank`, value: `Bank`, align: `center` },
                { text: `UPI`, value: `UPI`, align: `center` },
                { text: `Cheque`, value: `Cheque`, align: `center` },
              ]"
              :items="[
                {
                  Cash: $utils.currency_format(managementExpense.Cash),
                  Card: $utils.currency_format(managementExpense.Card),
                  Online: $utils.currency_format(managementExpense.Online),
                  Bank: $utils.currency_format(managementExpense.Bank),
                  UPI: $utils.currency_format(managementExpense.UPI),
                  Cheque: $utils.currency_format(managementExpense.Cheque),
                },
              ]"
            />
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="ProfitLossCardDialog" persistent max-width="700px">
      <AssetsIconClose left="690" @click="ProfitLossCardDialog = false" />
      <v-card>
        <v-alert class="grey lighten-3" dense>
          <span class="text-color">Profit & Loss</span>
        </v-alert>
        <v-card-text>
          <v-container>
            <AssetsTable
              :headers="[
                { text: `Profit`, value: `Profit`, align: `center` },
                { text: `Loss`, value: `Loss`, align: `center` },
                { text: `City Ledger`, value: `City Ledger`, align: `center` },
              ]"
              :items="[
                {
                  Profit: $utils.currency_format(profit),
                  Loss: $utils.currency_format(loss),
                  CityLedger: income.CityLedger,
                },
              ]"
            />
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-row dense>
      <v-col cols="3">
        <v-card
          @click="IncomeCardDialog = true"
          class="elevation-2"
          style="height: 230px"
        >
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Income
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
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
        <v-card
          @click="ExpenseCardDialog = true"
          class="elevation-2"
          style="height: 230px"
        >
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Expenses
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
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
        <v-card
          @click="ManagementCardDialog = true"
          class="elevation-2"
          style="height: 230px"
        >
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Management Expenses
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
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
        <v-card
          @click="ProfitLossCardDialog = true"
          class="elevation-2"
          style="height: 230px"
        >
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Profit and Loss
              </v-col>

              <v-col cols="4" class="text-right align-right"
                ><img
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
    </v-row>
    <v-card class="my-2 text-right">
      <v-btn
        :color="currentTabId === 1 ? `primary` : ''"
        text
        @click="currentTabId = 1"
        >Income</v-btn
      >
      <v-btn
        :color="currentTabId === 2 ? `primary` : ''"
        text
        @click="currentTabId = 2"
        >Expense</v-btn
      >
      <v-btn
        :color="currentTabId === 3 ? `primary` : ''"
        text
        @click="currentTabId = 3"
        >ManagementExpense</v-btn
      >
      <Income v-show="currentTabId == 1" @stats="(e) => (income = e)" />
      <ExpenseDashboard
        :is_admin_expense="0"
        v-show="currentTabId == 2"
        @stats="(e) => (expense = e)"
      />
      <ExpenseDashboard
        :is_admin_expense="1"
        v-show="currentTabId == 3"
        @stats="(e) => (managementExpense = e)"
      />
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    IncomeCardDialog: false,
    ExpenseCardDialog: false,
    ManagementCardDialog: false,
    ProfitLossCardDialog: false,

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
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
  },
};
</script>
