<template>
  <v-menu key="dateRange" ref="menu" v-model="menu" :close-on-content-click="false" transition="scale-transition"
    min-width="auto" :disabled="disabled">
    <template v-slot:activator="{ on }">
      <v-text-field :hide-details="true" outlined dense v-on="on" v-model="datesLabel" readonly
        placeholder="Select Date Range"></v-text-field>
    </template>

    <div class="text-center">
      <v-date-picker style="margin-top:20px" @input="firstPicker" persistant no-title color="primary" v-model="startDates"
        range scrollable>
      </v-date-picker>

      <v-date-picker style="margin-top:40px" @input="secondPicker" no-title color="primary" v-model="endDates" range
        :min="min">
      </v-date-picker>
      <v-card class="text-right  pb-2">

        <v-btn dark color="primary" small @click="clearDates">
          clear
        </v-btn>

        <v-btn dark color="primary" small @click="menu = false">
          close
        </v-btn>

        <v-btn dark color="primary" small @click="applyFilter()">
          Apply
        </v-btn>

      </v-card>

    </div>
  </v-menu>
</template>

<script>
let currentFullDate = new Date(
  Date.now() - new Date().getTimezoneOffset() * 60000
);

let nextFullDate = new Date(currentFullDate);
nextFullDate.setMonth(nextFullDate.getMonth() + 2);
let currentDate = currentFullDate.toISOString().slice(0, 10);
let nextDate = nextFullDate.toISOString().slice(0, 10);



export default {
  props: ["header", "column", "disabled", "DPStart_date", "DPEnd_date"],

  data: () => ({
    menu: false,
    menu2: false,

    showPicker: true,

    startDates: '',//[DPStart_date],// [currentDate],
    endDates: '',// [DPEnd_date],
    finalDates: [],
    min: nextDate,
  }),
  created() {
    this.startDates = [this.DPStart_date];
    this.endDates = [this.DPEnd_date];

    this.finalDates = [this.startDates[0], this.endDates[0]];
  },
  computed: {
    datesLabel() {


      return this.finalDates.join(" ~ ");
    },
  },
  watch: {
    startDates() {

      let nextFullDate = new Date(this.startDates[0]);
      nextFullDate.setMonth(nextFullDate.getMonth());

      //this.endDates[0] = nextFullDate.toISOString().slice(0, 10);
      this.min = nextFullDate.toISOString().slice(0, 10);

    },
  },
  methods: {
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    firstPicker() {
      this.startDates.sort((a, b) => a.localeCompare(b));
      this.endDates = [];
      this.finalDates = this.startDates;
      //this.$emit("selected-dates", this.finalDates);
      this.min = this.startDates[0];
    },
    secondPicker() {
      this.endDates.sort((a, b) => a.localeCompare(b));

      let endDate = this.endDates.length
        ? this.endDates[this.endDates.length - 1]
        : this.startDates[this.startDates.length - 1];

      this.finalDates = [this.startDates[0], endDate];

      this.startDates = this.finalDates;
      this.endDates = this.finalDates;

      //this.$emit("selected-dates", this.finalDates);

      //this.menu = false;

    },
    applyFilter() {
      this.$refs.menu.save(this.secondPicker);
      this.$emit("selected-dates", this.finalDates);

      this.menu = false;
    },
    clearDates() {
      this.clearStartDates();
      this.clearEndDates();
    },
    clearStartDates() {
      this.finalDates = [];
      this.startDates = [];
    },
    clearEndDates() {
      this.finalDates = [];
      this.endDates = [];
    },
  },
};
</script>
