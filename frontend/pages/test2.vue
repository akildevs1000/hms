<template>
  <v-row>
    <div class="wrapper">
      <span class="minus" @click="dec">-</span>
      <span class="num">{{ val }}</span>
      <span class="plus" @click="inc">+</span>
    </div>
    <!--
    <v-col cols="12">
      <FullCalendar :options="calendarOptions" style="background:#fff;" />
    </v-col> -->
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
      val: 0,
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
          { id: "103", room_no: "103", eventBorderColor: "white" },
          {
            id: "104",
            room_no: "104",
            eventBorderColor: "white"
          }
        ],
        events: [
          {
            id: "1",
            room_id: "1",
            resourceId: "104",
            start: "2022-11-09 00:00:00",
            end: "2022-11-11 06:00:00",
            title: "e",
            background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)",
            eventBorderColor: "red"
          },
          {
            id: "1",
            room_id: "1",
            resourceId: "103",
            start: "2022-11-09 00:00:00",
            end: "2022-11-11 06:00:00",
            title: "e",
            background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)",
            eventBorderColor: "red"
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
    inc() {
      this.val++;
      this.val = this.val < 10 ? "0" + this.val : this.val;
      this.val = this.val;
    },

    dec() {
      if (this.val > 1) {
        this.val--;
        this.val = this.val < 10 ? "0" + this.val : this.val;
        this.val = this.val;
      }
    },

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
/* Google fonts import link */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
body {
  height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #343f4f;
}
.wrapper {
  height: 120px;
  min-width: 380px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
}
.wrapper span {
  width: 100%;
  text-align: center;
  font-size: 55px;
  font-weight: 600;
  cursor: pointer;
  user-select: none;
}
.wrapper span.num {
  font-size: 50px;
  border-right: 2px solid rgba(0, 0, 0, 0.2);
  border-left: 2px solid rgba(0, 0, 0, 0.2);
  pointer-events: none;
}
</style>
