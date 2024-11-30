<template>
  <div v-if="can(`chat_access`)">
    <v-card class="px-1">
      <Chat />
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
