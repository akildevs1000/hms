<template>
  <div v-if="can('income_access')">
    <v-row dense>
      
      <v-col cols="12">
        <v-card class="my-2">
          <div
            style="display: flex; justify-content: right; align-items: right"
            class="pa-2"
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
        <Cash
          :filters="filters"
          @stats="handleIncome"
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

      this.getProfitLoss();
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
