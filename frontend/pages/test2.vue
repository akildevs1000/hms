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
        now: "2022-11-07",
        editable: true,
        aspectRatio: 1.8,
        scrollTime: "00:00",
        displayEventTime: false,

        initialView: "resourceTimelineMonth",

        navLinks: true,
        resourceAreaWidth: "15%",

        resourceAreaColumns: [
          {
            headerContent: "Room",
            field: "room_no"
          },
          {
            headerContent: "Room Type",
            field: "room_type"
          }
        ],
        resources: [
          { id: "103", room_no: "103", eventColor: "green" },
          {
            id: "104",
            room_no: "104",
            eventColor: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)"
          }
        ],
        events: [
          {
            id: "1",
            room_id: "1",
            resourceId: "104",
            start: "2022-11-09 00:00:00",
            end: "2022-11-11 06:00:00",
            title: "e"
          },
          {
            id: "1",
            room_id: "1",
            resourceId: "103",
            start: "2022-11-09 00:00:00",
            end: "2022-11-11 06:00:00",
            title: "e",
            background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)"
          }
        ],
        eventDidMount: function(info) {
          if (info.event.extendedProps.background) {
            info.el.style.background = info.event.extendedProps.background;
          }
        }
      },
      data: []
    };
  },
  created() {
    console.log(this.$auth.user.id);
  },

  mounted() {
    // this.room_types();
    // this.get_events();
  },

  methods: {
    room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`room_list`, payload).then(({ data }) => {
        this.calendarOptions.resources = data;
      });
    },

    get_events() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`events_list`, payload).then(({ data }) => {
        this.calendarOptions.events = data;
      });
    }
  }
};
</script>

<style scoped>
.app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}
</style>
