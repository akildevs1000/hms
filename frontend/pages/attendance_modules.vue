<template>
  <div>
    <v-row class="mt-5">
      <v-col md="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col cols="12">
        <Cards :columns="4" :cards="sub_modules" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    Model: "Attendance",
    sub_modules: [
      {
        id: 1,
        heading: "Employees Schedule",
        subheading: "Attendance Management",
        name: "Employees Schedule",
        icon: "mdi-account",
        link: "employee_schedule",
      },
      {
        id: 2,
        heading: "Shift Management",
        subheading: "Attendance Management",
        name: "Schedule",
        icon: "mdi-clock",
        link: "shift",
      },
      {
        id: 3,
        heading: "Time Slot",
        subheading: "Attendance Management",
        name: "Schedule Management",
        icon: "mdi-clock",
        link: "shift",
      },
    ],
  }),
};
</script>
