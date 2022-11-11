<template>
  <v-row>
    <v-col cols="12">
      <FullCalendar :options="calendarOptions" style="background:#fff;" />
    </v-col>
  </v-row>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";

export default {
  components: {
    FullCalendar
  },
  data() {
    return {
      calendarOptions: {
        plugins: [interactionPlugin, dayGridPlugin, resourceTimelinePlugin],
        now: "2020-09-07",
        editable: true,
        aspectRatio: 1.8,
        scrollTime: "00:00",

        initialView: "resourceTimelineMonth",

        navLinks: true,
        resourceAreaWidth: "15%",

        resourceAreaColumns: [
          {
            headerContent: "Room",
            field: "room"
          },
          {
            headerContent: "Room Type",
            field: "room_type"
          }
        ],
        resources: [
          { id: "a", room: "102" },
          { id: "b", room: "103", eventColor: "green" },
          { id: "c", room: "104", eventColor: "orange" },
          { id: "e", room: "105" },
          { id: "f", room: "106", eventColor: "red" },
          { id: "g", room: "102" },
          { id: "h", room: "102" },
          { id: "i", room: "102" },
          { id: "j", room: "102" },
          { id: "k", room: "102" },
          { id: "l", room: "102" },
          { id: "m", room: "102" },
          { id: "n", room: "102" },
          { id: "o", room: "102" },
          { id: "p", room: "102" },
          { id: "q", room: "102" },
          { id: "r", room: "102" },
          { id: "s", room: "102" },
          { id: "t", room: "102" },
          { id: "u", room: "102" },
          { id: "v", room: "102" },
          { id: "w", room: "102" },
          { id: "x", room: "102" },
          { id: "y", room: "102" },
          { id: "z", room: "102" }
        ],
        events: [
          {
            id: "1",
            resourceId: "b",
            start: "2020-09-07T02:00:00",
            end: "2020-09-07T07:00:00",
            title: "e"
          },
          {
            id: "2",
            resourceId: "c",
            start: "2020-09-07T05:00:00",
            end: "2020-09-07T22:00:00",
            title: "event 2"
          },
          {
            id: "3",
            resourceId: "d",
            start: "2020-09-06",
            end: "2020-09-08",
            title: "event 3"
          },
          {
            id: "4",
            resourceId: "e",
            start: "2020-09-07T03:00:00",
            end: "2020-09-07T08:00:00",
            title: "event 4"
          },
          {
            id: "5",
            resourceId: "f",
            start: "2020-09-07T00:30:00",
            end: "2020-09-07T02:30:00",
            title: "event 5"
          }
        ]
      }
    };
  }
};
</script>

<style scoped>
.app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}
</style>
