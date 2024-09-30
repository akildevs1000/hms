<template>
  <div>
    <v-row>
      <v-col md="9" v-if="customer_id">
        <CustomerIndex :customer_id="customer_id" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    customer_id: 0,
  }),
  created() {
    this.loading = true;
    this.id_card_type_id = this.customer.id_card_type_id;
    this.getData();
  },
  methods: {
    getData() {
      let id = this.$route.params.id;
      this.$axios.get(`get_customer_history/${id}`).then(({ data }) => {
        this.customer_id = data.data.id;
        this.customer = data.data;
        this.bookings = data.data.bookings;
        this.revenue = data.revenue;
        this.city_ledger = data.city_ledger;
        this.loading = false;
      });
    },
  },
};
</script>