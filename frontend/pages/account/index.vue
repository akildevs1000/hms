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
              ]"
              :items="[
                {
                  Cash: $utils.currency_format(expense.Cash),
                  Card: $utils.currency_format(expense.Card),
                  Online: $utils.currency_format(expense.Online),
                  Bank: $utils.currency_format(expense.Bank),
                  UPI: $utils.currency_format(expense.UPI),
                  Cheque: $utils.currency_format(expense.Cheque),
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
              v-if="ProfilLoss"
              :headers="[
                { text: `Profit`, value: `Profit`, align: `center` },
                { text: `Loss`, value: `Loss`, align: `center` },
                { text: `City Ledger`, value: `CityLedger`, align: `center` },
              ]"
              :items="[
                {
                  Profit: $utils.currency_format(ProfilLoss.profit),
                  Loss: $utils.currency_format(ProfilLoss.loss),
                  CityLedger: $utils.currency_format(ProfilLoss.cityLedger),
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
          v-if="income"
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
              :key="key + 1"
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
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3">
        <v-card
          v-if="expense"
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
              :key="key + 2"
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
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3">
        <v-card
          v-if="managementExpense"
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
              :key="key + 3"
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
              v-if="ProfilLoss"
              :key="key + 4"
              :width="'200px'"
              showPriceFormat="true"
              name="margin"
              size="100%"
              :total="'100'"
              :colors="colors"
              :labels="[
                { color: `#4caf50`, text: `Profit`, value: ProfilLoss.profit },
                {
                  color: `#538234`,
                  text: `Loss`,
                  value: ProfilLoss.loss,
                },
                {
                  color: `#0f642b`,
                  text: `City Ledger`,
                  value: ProfilLoss.cityLedger,
                },
              ]"
            />
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12">
        <v-card class="my-2">
          <div
            style="display: flex; justify-content: right; align-items: right"
            class="pa-2"
          >
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
            <div style="padding-top: 1px">
              <v-text-field
                label="Search..."
                dense
                outlined
                flat
                append-icon="mdi-magnify"
                v-model="search"
                @input="searchIt"
                hide-details
                style="max-width: 200px"
              ></v-text-field>
            </div>
            &nbsp;
            <FilterDateRange :defaultDates="true" @filter-attr="filterAttr" />
          </div>
        </v-card>
      </v-col>
      <v-col cols="12">
        <Income
          v-show="currentTabId == 1"
          :filters="filters"
          @stats="handleIncome"
        />
        <ExpenseDashboard
          :is_admin_expense="0"
          v-show="currentTabId == 2"
          :filters="filters"
          @stats="handleNonMagementExpense"
        />
        <ExpenseDashboard
          :is_admin_expense="1"
          v-show="currentTabId == 3"
          :filters="filters"
          @stats="handleMagementExpense"
        />
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    search: null,
    filters: {
      from: new Date().toJSON().slice(0, 10),
      to: new Date().toJSON().slice(0, 10),
    },
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
    activeTab: 0,
    income: null,
    expense: null,
    managementExpense: null,
    loading: false,
    ProfilLoss: null,
  }),
  async created() {
    this.loading = true;
    await this.getProfitLoss();
  },

  methods: {
    handleIncome(e) {
      this.income = e;
    },
    handleNonMagementExpense(e) {
      this.expense = e;
    },
    handleMagementExpense(e) {
      this.managementExpense = e;
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    filterAttr(data) {
      this.filters = {
        from: data.from,
        to: data.to,
        search: this.search,
      };
    },
    searchIt() {
      if (this.search.length == 0) {
        this.filters = {
          ...this.filters,
          search: this.search,
        };
      } else if (this.search.length > 2) {
        this.filters = {
          ...this.filters,
          search: this.search,
        };
      }
    },
    async getProfitLoss() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
          from_date: this.filters.from,
          to_date: this.filters.to,
        },
      };

      let { data } = await this.$axios.get(`profit-loss`, config);

      this.ProfilLoss = data;
    },
  },
};
</script>
