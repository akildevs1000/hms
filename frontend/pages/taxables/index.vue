<template>
  <v-card class="mt-7">
       <div style="display: flex; align-items: right;justify-content: end;" class="pa-5">
        <div>
          <v-text-field
            label="Search..."
            dense
            outlined
            flat
            append-icon="mdi-magnify"
            @input="searchIt"
            v-model="search"
            hide-details
            style="max-width: 200px"
          ></v-text-field>
        </div>
        &nbsp;
        <FilterDateRange :defaultDates="true" @filter-attr="filterAttr" />
      </div>
      <Taxable
        :filter="filter"
        :TabId="currentTabId"
        :endpoint="'get_taxable_invoices'"
        :Model="'All Guest'"
        @response="handleResponse"
      />
    </v-card>
</template>

<script>
export default {
  data: () => ({
    endpoint: null,
    currentTabId: 4,
    amountStats: 1,
    activeTab: 0,
    stats: [],
    totalStats: [],
    statsForInHouse: [],
    statsForCheckOut: [],
    statsForReservation: [],
    search: null,
    filter: null,
  }),
  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filter = {
        from: data.from,
        to: data.to,
        search: this.search,
      };
    },
    searchIt() {
      if (this.search.length == 0) {
        this.filter = {
          ...this.filter,
          search: this.search,
        };
      } else if (this.search.length > 0) {
        this.filter = {
          ...this.filter,
          search: this.search,
        };
      }
    },
    handleResponse(e) {
      this.stats = e.stats;
      this.totalStats = e.totalStats;
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
