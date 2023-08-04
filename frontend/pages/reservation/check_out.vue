
<template>
  <div v-if="can('checkout_access') && can('checkout_view')">
    <ReservationAllList :endpoint="'check_out_reservation_list'" :Model="' Check out Reservation'" />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
  }
}

</script>

