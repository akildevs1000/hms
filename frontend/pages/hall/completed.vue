

<template>
    <div v-if="can('party_hall') && can('party_hall')">
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