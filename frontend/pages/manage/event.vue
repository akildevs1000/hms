<template>
  <v-row class="fill-height">
    <v-col cols="12" sm="6" md="4">
      <v-text-field
        label="Outlined"
        placeholder="Placeholder"
        outlined
        dense
      ></v-text-field>
    </v-col>

    <template>
      <v-form>
        <v-container>
          <v-row>
            <v-col cols="12" sm="6" md="4">
              <v-text-field dense label="Regular"></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" md="4">
              <v-text-field label="Filled" filled dense></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" md="4">
              <v-text-field
                label="Filled"
                placeholder="Dense & Rounded"
                filled
                rounded
                dense
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" md="4">
              <v-text-field label="Solo" solo dense></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" md="4">
              <v-text-field label="Outlined" outlined dense></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" md="4">
              <v-text-field
                label="Outlined"
                placeholder="Placeholder"
                outlined
                dense
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-form>
    </template>
  </v-row>
</template>

<script>
export default {
  data: () => ({
    yourLabel: "ggg",
    focus: "",
    type: "month",
    typeToLabel: {
      month: "Month",
      week: "Week",
      day: "Day",
      "4day": "4 Days"
    },
    selectedEvent: {},
    selectedElement: null,
    selectedOpen: false,
    events: [],
    colors: [
      "blue",
      "indigo",
      "deep-purple",
      "cyan",
      "green",
      "orange",
      "grey darken-1"
    ],
    names: [
      "Meeting",
      "Holiday",
      "PTO",
      "Travel",
      "Event",
      "Birthday",
      "Conference",
      "Party"
    ]
  }),
  mounted() {
    // this.$refs.calendar.checkChange();
  },
  methods: {
    viewDay({ date }) {
      this.focus = date;
      this.type = "day";
    },
    getEventColor(event) {
      return event.color;
    },
    setToday() {
      this.focus = "";
    },
    prev() {
      this.$refs.calendar.prev();
    },
    next() {
      this.$refs.calendar.next();
    },
    showEvent({ nativeEvent, event }) {
      const open = () => {
        this.selectedEvent = event;
        this.selectedElement = nativeEvent.target;
        requestAnimationFrame(() =>
          requestAnimationFrame(() => (this.selectedOpen = true))
        );
      };

      if (this.selectedOpen) {
        this.selectedOpen = false;
        requestAnimationFrame(() => requestAnimationFrame(() => open()));
      } else {
        open();
      }

      nativeEvent.stopPropagation();
    },
    updateRange({ start, end }) {
      // const events = [];

      // const min = new Date(`${start.date}T00:00:00`);
      // const max = new Date(`${end.date}T23:59:59`);
      // const days = (max.getTime() - min.getTime()) / 86400000;
      // const eventCount = this.rnd(days, days + 20);

      // for (let i = 0; i < eventCount; i++) {
      //   const allDay = this.rnd(0, 3) === 0;
      //   const firstTimestamp = this.rnd(min.getTime(), max.getTime());
      //   const first = new Date(firstTimestamp - (firstTimestamp % 900000));
      //   const secondTimestamp = this.rnd(2, allDay ? 288 : 8) * 900000;
      //   const second = new Date(first.getTime() + secondTimestamp);

      //   events.push({
      //     name: this.names[this.rnd(0, this.names.length - 1)],
      //     start: first,
      //     end: second,
      //     color: this.colors[this.rnd(0, this.colors.length - 1)],
      //     timed: !allDay
      //   });
      // }

      // this.events = [
      //   {
      //     name: "Event",
      //     start: "2023-01-19T04:45:00.000Z",
      //     end: "2023-01-19T05:45:00.000Z",
      //     color: "orange",
      //     timed: true
      //   },
      //   {
      //     name: "Travel",
      //     start: "2023-01-20T10:00:00.000Z",
      //     end: "2023-01-20T11:30:00.000Z",
      //     color: "orange",
      //     timed: true
      //   }
      // ];

      // this.events = events;
      // console.log(this.events);

      this.events = [
        {
          name: "Eid Festival",
          start: "2023-01-19",
          end: "2023-01-25",
          color: "deep-purple",
          timed: true
        },
        {
          name: "Pongal Festival",
          start: "2023-01-05",
          end: "2023-01-10",
          color: "indigo",
          timed: true
        },
        {
          name: "Republic Festival",
          start: "2023-01-11",
          end: "2023-01-14",
          color: "indigo",
          timed: true
        }
      ];
    },
    rnd(a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a;
    }
  }
};
</script>

<style>
.align-with-border {
  top: -14px;
}
</style>
