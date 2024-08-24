<template>
  <div>
    <BookingModifyHall v-if="isHall" :BookedRoomId="BookedRoomId" />
    <BookingModifyRoom v-else :BookedRoomId="BookedRoomId" />
  </div>
</template>
<script>
export default {
  props: ["BookedRoomId", "isHall"],
};
</script>
