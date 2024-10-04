<template>
  <v-menu
    v-model="dateMenu"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    min-width="auto"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        :label="label || 'Date'"
        dense
        outlined
        readonly
        v-bind="attrs"
        v-on="on"
        v-model="date"
        hide-details
      ></v-text-field>
    </template>
    <v-date-picker
      v-model="date"
      @input="CustomFilter"
      no-title
      color="primary"
    ></v-date-picker>
  </v-menu>
</template>

<script>
export default {
  props: ["label", "defaultDate"],
  data: () => ({
    dateMenu: false,
    date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
  }),
  created() {
    if (this.defaultDate) {
      this.date = this.defaultDate;
    }
  },
  methods: {
    CustomFilter() {
      this.dateMenu = false;
      this.$emit("date", this.date);
    },
  },
};
</script>
