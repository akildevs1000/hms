<template>
  <div>
    <BookingModifyHall v-if="isHall" :BookedRoomId="BookedRoomId" @close-calender-hall="$emit(`close`)" />
    <BookingModifyRoom v-else :BookedRoomId="BookedRoomId" @close-calender-room="$emit(`close`)" />
  </div>
</template>
<script>
export default {
  props: ["BookedRoomId", "isHall"],
};
</script>
