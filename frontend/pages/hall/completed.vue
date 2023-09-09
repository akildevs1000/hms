

<template>
    <div v-if="can('in_house_access') && can('in_house_view')">
        <HallReservationList :endpoint="'hall/completed_reservation_list'" :Model="'Completed Reservation'" />
    </div>
    <NoAccess v-else />
</template>
<script>
import HallReservationList from '../../components/partyhall/HallReservationList.vue';

export default {
    methods: {
        can(per) {
            let u = this.$auth.user;
            return ((u && u.permissions.some(e => e == per || per == "/")) || u.is_master);
        },
    },
    components: { HallReservationList }
}

</script>