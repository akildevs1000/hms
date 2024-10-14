<template>
  <div class="pt-2">
    <v-card class="px-2">
      <v-row>
        <v-col v-for="(stat, index) in stats" :key="index">
          <AssetsCard :key="currentTabId" :options="stat" />
        </v-col>
      </v-row>
    </v-card>

    <v-card class="mt-7">
      <v-row>
        <v-col class="pl-5">
          <div class="">
            <AssetsIcon
              icon="printer-outline"
              @click="process('reservation_report_print', endpoint)"
            />
            &nbsp;
            <AssetsIcon
              icon="download-outline"
              @click="process('reservation_report_download', endpoint)"
            />
          </div>
        </v-col>
        <v-col cols="2"></v-col>
        <v-col cols="7" class="text-right">
          <div style="display: flex; align-items: right" class="pr-2">
            <v-btn
              :color="currentTabId === 1 ? 'primary' : ''"
              text
              @click="setCurrentTab(1)"
            >
              In House
            </v-btn>
            <v-btn
              :color="currentTabId === 2 ? 'primary' : ''"
              text
              @click="setCurrentTab(2)"
            >
              Checkout
            </v-btn>
            <v-btn
              :color="currentTabId === 3 ? 'primary' : ''"
              text
              @click="setCurrentTab(3)"
            >
              Reservation
            </v-btn>
            <v-btn
              :color="currentTabId === 4 ? 'primary' : ''"
              text
              @click="setCurrentTab(4)"
            >
              All Guest
            </v-btn>

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
        </v-col>
      </v-row>
    </v-card>

    <v-card class="mt-7">
      <ReservationAllList
        v-if="currentTabId == 1"
        :filter="filter"
        :TabId="currentTabId"
        :endpoint="'in_house_reservation_list'"
        :Model="'In House'"
        @response="handleResponse"
      />
      <ReservationAllList
        v-if="currentTabId == 2"
        :filter="filter"
        :TabId="currentTabId"
        :endpoint="'check_out_reservation_list'"
        :Model="' Check out Reservation'"
        @response="handleResponse"
      />
      <ReservationAllList
        v-if="currentTabId == 3"
        :filter="filter"
        :TabId="currentTabId"
        :endpoint="'up_coming_reservation_list'"
        :Model="'Reservation'"
        @response="handleResponse"
      />
      <ReservationAllList
        v-if="currentTabId == 4"
        :filter="filter"
        :TabId="currentTabId"
        :endpoint="'all_reservation_list'"
        :Model="'All Guest'"
        @response="handleResponse"
      />
    </v-card>

    <v-card class="mt-5 px-2">
      <v-row>
        <v-col v-for="(stat, index) in totalStats" :key="index">
          <AssetsCardBottom :key="amountStats + index" :options="stat" />
        </v-col>
      </v-row>
    </v-card>
  </div>
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
      } else if (this.search.length > 2) {
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
