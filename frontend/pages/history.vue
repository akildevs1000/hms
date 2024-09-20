<template>
  <v-tabs right>
    <v-tab>In House</v-tab>
    <v-tab>Checkout</v-tab>
    <v-tab>Reservation</v-tab>
    <v-tab>All Guest</v-tab>
    <v-tab-item>
      <div v-if="can('in_house_access') && can('in_house_view')">
        <ReservationAllList
          :endpoint="'in_house_reservation_list'"
          :Model="'In House'"
        /></div
    ></v-tab-item>
    <v-tab-item>
      <div v-if="can('checkout_access') && can('checkout_view')">
        <ReservationAllList
          :endpoint="'check_out_reservation_list'"
          :Model="' Check out Reservation'"
        />
      </div>
    </v-tab-item>
    <v-tab-item>
      <div v-if="can('reservation_access') && can('reservation_view')">
        <ReservationAllList
          :endpoint="'up_coming_reservation_list'"
          :Model="'Reservation'"
        />
      </div>
    </v-tab-item>
    <v-tab-item>
      <div v-if="can('reservation_access') && can('reservation_view')">
        <ReservationAllList
          :endpoint="'all_reservation_list'"
          :Model="'All Guest'"
        />
      </div>
    </v-tab-item>
  </v-tabs>
</template>

<script>
export default {
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
