

<template>
  <div v-if="can('in_house_access') && can('in_house_view')">
    <ReservationAllList :endpoint="'in_house_reservation_list'" :Model="'In House Reservation'" />
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
