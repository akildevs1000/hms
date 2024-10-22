<template>
  <div>
    <date-picker
      style="width: 100%"
      value-type="format"
      format="YYYY-MM-DD"
      type="date"
      v-model="dateRange"
      @change="CustomFilter()"
      range
    ></date-picker>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: {
    DatePicker,
  },
  props: ["defaultFilterType", "height", "defaultDates"],
  data() {
    return {
      // -------------------end chart ----------------
      dateRange: null,
      from_date: "",
      from_menu: false,

      to_date: "",
      to_menu: false,
      loading: false,
      showTimePanel: false,
      filterType: 1,
    };
  },

  mounted() {
    // document.querySelector(".mx-input").focus();
  },
  created() {
    const today = new Date();

    // Calculate from_date as today - 7 days
    const from_date = new Date(today);
    from_date.setDate(today.getDate() - 6);

    // Calculate to_date as today + 7 days
    const to_date = new Date(today);
    to_date.setDate(today.getDate() + 7);

    this.from_date = from_date.toISOString().slice(0, 10);
    this.to_date = to_date.toISOString().slice(0, 10);

    this.dateRange = [this.from_date, this.to_date];

    if (this.defaultDates) {
      this.from_date = new Date().toJSON().slice(0, 10);
      this.to_date = new Date().toJSON().slice(0, 10);

      this.dateRange = [this.from_date, this.to_date];

      let data = {
        from: this.from_date,
        to: this.to_date,
        type: 1,
      };

      this.$emit("filter-attr", data);
    }
  },

  methods: {
    CustomFilter() {
      this.from_date = this.dateRange[0];
      this.to_date = this.dateRange[1];
      if (this.from_date && this.to_date) {
        let data = {
          from: this.from_date,
          to: this.to_date,
          type: this.filterType,
        };

        this.$emit("filter-attr", data);
      }
    },
  },
};
</script>
