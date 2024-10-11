<template>
  <v-row>
    <v-col :cols="filterType == 5 ? '6' : ''">
      <v-autocomplete
        v-model="filterType"
        :items="[
          {
            id: -1,
            name: 'All',
          },
          {
            id: 1,
            name: 'Today',
          },
          {
            id: 2,
            name: 'Yesterday',
          },
          {
            id: 3,
            name: 'This Week',
          },
          {
            id: 4,
            name: 'This Month',
          },
          {
            id: 5,
            name: 'Custom',
          },
        ]"
        dense
        placeholder="Date"
        outlined
        :hide-details="true"
        item-text="name"
        item-value="id"
      ></v-autocomplete>
    </v-col>
    <v-col cols="6" v-if="filterType == 5">
      <date-picker
        style="width: 100%"
        value-type="format"
        format="YYYY-MM-DD"
        type="date"
        v-model="time3"
        @change="CustomFilter()"
        range
      ></date-picker>
    </v-col>
  </v-row>
</template>

<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: {
    DatePicker,
  },
  props: ["defaultFilterType"],
  data() {
    return {
      // -------------------end chart ----------------
      time3: null,
      from_date: "",
      from_menu: false,

      to_date: "",
      to_menu: false,
      loading: false,
      showTimePanel: false,
      filterType: 1,
      search: "",
    };
  },

  watch: {
    filterType() {
      this.showTimePanel = true;
      this.FilterData();
    },
  },

  mounted() {
    if (this.filterType == 5) document.querySelector(".mx-input").focus();
  },
  created() {
    if (this.defaultFilterType) {
      this.filterType = this.defaultFilterType;
    }

    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
    this.to_date = today.toISOString().slice(0, 10);

    this.time3 = [this.from_date, this.to_date];
  },
  methods: {
    commonMethod() {
      if (this.from_date && this.to_date) {
      }
    },
    CustomFilter() {
      this.from_date = this.time3[0];
      this.to_date = this.time3[1];
      if (this.from_date && this.to_date) {
        let data = {
          from: this.from_date,
          to: this.to_date,
          type: this.filterType,
          search: this.search,
        };

        this.$emit("filter-attr", data);
      }
    },
    FilterData() {
      this.from_date = this.time3[0];
      this.to_date = this.time3[1];
      const today = new Date();

      if (this.filterType == 1) {
        // Get today's date
        this.from_date = today.toISOString().slice(0, 10);
        this.to_date = today.toISOString().slice(0, 10);
      } else if (this.filterType == 2) {
        // Get yesterday's date
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);
        this.from_date = yesterday.toISOString().slice(0, 10);
        this.to_date = yesterday.toISOString().slice(0, 10);
      } else if (this.filterType == 3) {
        // Get the first day of the current week (Sunday)
        const firstDayOfWeek = new Date(today);
        firstDayOfWeek.setDate(today.getDate() - today.getDay());

        // Get the last day of the current week (Saturday)
        const lastDayOfWeek = new Date(today);
        lastDayOfWeek.setDate(today.getDate() - today.getDay() + 6);

        this.from_date = firstDayOfWeek.toISOString().slice(0, 10);
        this.to_date = lastDayOfWeek.toISOString().slice(0, 10);
      } else if (this.filterType == 4) {
        // const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

        // // Get the last day of the current month
        // const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        // this.from_date = firstDayOfMonth.toISOString().slice(0, 10);
        // this.to_date = lastDayOfMonth.toISOString().slice(0, 10);

        this.from_date = this.formatDate(
          new Date(today.getFullYear(), today.getMonth(), 1)
        );
        this.to_date = this.formatDate(
          new Date(today.getFullYear(), today.getMonth() + 1, 0)
        );
      } else if (this.filterType == 5) {
        this.time3 = [];

        return;
      }

      if (this.from_date && this.to_date) {
        let data = {
          from: this.from_date,
          to: this.to_date,
          type: this.filterType,
          search: this.search,
        };

        this.$emit("filter-attr", data);
      }
    },

    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return (
        year +
        "-" +
        (month < 10 ? "0" : "") +
        month +
        "-" +
        (day < 10 ? "0" : "") +
        day
      );
    },
  },
};
</script>

<style>
.mx-input {
  height: 40px !important;
  border: 1px solid #9e9e9e !important;
  color: black !important;
}

.mx-table-date td,
.mx-table-date th {
  text-align: center !important;
}
</style>
