<template>
  <div v-if="can(`customers_access`)">
    <v-card class="px-2 mt-7">
      <v-row>
        <v-col v-for="(stat, index) in stats" :key="index">
          <AssetsCard :key="index" :options="stat" />
        </v-col>
      </v-row>
    </v-card>
    <v-card class="mt-7 px-2">
      <v-tabs right>
        <v-tab>Online</v-tab>
        <v-tab>Corporate</v-tab>
        <v-tab>Travel Agent</v-tab>
        <v-tab>Customer</v-tab>
        <v-tab-item>
          <Source type="Online" />
        </v-tab-item>
        <v-tab-item>
          <Source @response="getBookingStats" type="Corporate" />
        </v-tab-item>
        <v-tab-item>
          <Source type="Travel Agency" />
        </v-tab-item>
        <v-tab-item>
          <Customer @response="getBookingStats" />
        </v-tab-item>
      </v-tabs>
    </v-card>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    stats: [],
  }),
  async created() {
    await this.getBookingStats();
  },
  methods: {
    async getBookingStats() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      let { data } = await this.$axios.get(`get-bookings-source-type`, config);
      this.stats = data;
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
