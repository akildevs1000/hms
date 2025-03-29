<template>
  <v-container fluid>
    <v-card>
      <v-container fluid>
        <v-row>
          <v-col md="7"> Invoices </v-col>
          <v-col md="5" class="d-flex justify-end">
            <!-- Aligning this row to the right -->
            <v-row justify="end">
              <v-col>
                <v-text-field
                  label="Search..."
                  dense
                  outlined
                  flat
                  append-icon="mdi-magnify"
                  @input="searchIt"
                  v-model="search"
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col>
                <v-autocomplete
                  dense
                  outlined
                  flat
                  v-model="status"
                  :items="['Select All', 'Unpaid', 'Paid']"
                  hide-details
                  @change="filterAttr"
                ></v-autocomplete>
              </v-col>
              <v-col>
                <FilterDateRange @filter-attr="filterAttr" />
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
    <v-card class="mt-5">
      <v-row>
        <v-col cols="12">
          <BookingInvoices
            :filter="filter"
            :TabId="currentTabId"
            :endpoint="'booking_invoices'"
            :Model="'All Guest'"
            @response="handleResponse"
          />
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    endpoint: null,
    currentTabId: 1,
    amountStats: 1,
    activeTab: 0,
    stats: [],
    totalStats: [],
    statsForInHouse: [],
    statsForCheckOut: [],
    statsForReservation: [],
    search: null,
    status: "Select All",
    filter: null,
  }),
  methods: {
    filterAttr(data) {
      this.filter = {
        ...this.filter,
        ...data,
        search: this.search,
        status: this.status,
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
    setCurrentTab(id) {
      this.currentTabId = id; // Update the currentTabId to the clicked tab's ID
      this.filter = {
        ...this.filter,
        search: this.search,
      };
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
