<template>
  <div id="calendar">
    <div class="calendar-header">
      <button @click="prevMonth">Prev</button>
      <span>{{ currentMonth }} {{ currentYear }}</span>
      <button @click="nextMonth">Next</button>
    </div>
    <table>
      <thead>
        <tr>
          <th v-for="day in weekdays" :key="day">{{ day }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(week, index) in weeks" :key="index">
          <td
            v-for="day in week"
            :key="day.date"
            :class="{ current: day.current }"
          >
            {{ day.date }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentYear: new Date().getFullYear(),
      currentMonth: new Date().getMonth(),
      weekdays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
      weeks: []
    };
  },
  created() {
    this.renderCalendar();
  },
  methods: {
    prevMonth() {
      this.currentMonth--;
      if (this.currentMonth < 0) {
        this.currentMonth = 11;
        this.currentYear--;
      }
      this.renderCalendar();
    },
    nextMonth() {
      this.currentMonth++;
      if (this.currentMonth > 11) {
        this.currentMonth = 0;
        this.currentYear++;
      }
      this.renderCalendar();
    },
    renderCalendar() {
      // code to render the calendar for the current month and year goes here
    }
  }
};
</script>

<style>
/* calendar styles go here */
</style>
