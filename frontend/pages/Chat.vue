<template>
  <v-card>
    <Chat />
  </v-card>
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
  },
};
</script>
