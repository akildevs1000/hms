
<template>
  <div v-if="can('reservation_access') && can('reservation_view')">
    <ReservationAllList :endpoint="'up_coming_reservation_list'" :Model="'Reservation'" />
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
