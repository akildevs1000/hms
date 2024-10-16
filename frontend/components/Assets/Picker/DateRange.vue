<template>
  <v-menu
    v-model="monthlyMenu"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    min-width="auto"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        label="Select Month"
        dense
        outlined
        readonly
        v-bind="attrs"
        v-on="on"
        v-model="months"
        hide-details
      ></v-text-field>
    </template>
    <v-date-picker
      v-model="months"
      range
      @input="CustomFilter"
      no-title
      show-adjacent-months
      color="primary"
    ></v-date-picker>
  </v-menu>
</template>

<script>
const getFirstAndLastMonth = () => {
  const fullYear = new Date().getFullYear();
  return [`${fullYear}-01`, `${fullYear}-12`];
};
export default {
  data: () => ({
    monthlyMenu: false,
    months: getFirstAndLastMonth(),
  }),
  methods: {
    CustomFilter() {
      if (this.months.length == 2) {
        this.monthlyMenu = false;
        this.$emit("months", this.months);
      }
    },
  },
};
</script>
