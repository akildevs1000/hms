<template>
  <div>
    <v-row>
      <!-- <v-col xs="12" sm="12" md="2" cols="12">
          <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" v-model="search"
            hide-details></v-text-field>
        </v-col> -->
      <!-- <v-col xs="12" sm="6" md="6" cols="12">
        <v-select
          v-model="filterType"
          :items="[
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
        ></v-select>
      </v-col> 
      <v-col md="4" sm="6" class="box" style="padding-top: 7px;">-->

      <date-picker
        style="padding-top: 7px; width: 230px"
        value-type="format"
        format="YYYY-MM-DD"
        type="date"
        v-model="time3"
        @change="CustomFilter()"
        range
      ></date-picker>
      <!--</v-col>
       <v-col md="3" v-if="filterType == 5">
          <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
            offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
                class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
            </template>
            <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
          </v-menu>
        </v-col>
        <v-col md="3" v-if="filterType == 5">
          <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
            min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense class="custom-text-box shadow-none"
                solo flat label="To" :hide-details="true"></v-text-field>
            </template>
            <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
          </v-menu>
        </v-col> -->
    </v-row>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: {
    DatePicker,
  },
  props: [
    "defaultFilterType",
    "height",
    "width",
    "default_date_from",
    "default_date_to",
  ],
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

      // if (this.filterType == 5) {
      //   setTimeout(() => {
      //     document.addEventListener("DOMContentLoaded", function () {
      //       const textBox = document.querySelector('.mx-input');

      //       if (textBox) {
      //         textBox.focus();
      //       }
      //     });
      //   }, 5000);

      // }
    },
    // search() {
    //   this.FilterData();
    // },
    // from_date() {
    //   this.FilterData();
    // },
    // to_date() {
    //   this.FilterData();
    // },
  },

  mounted() {
    if (this.filterType == 5) document.querySelector(".mx-input").focus();

    const elementsArray = document.getElementsByClassName("mx-input");

    if (this.height && this.height != "") {
      elementsArray[0].style.height = this.height;
    }
    if (this.width && this.width != "") {
      elementsArray[0].style.width = this.width;
    }

    if (this.default_date_from && this.default_date_to) {
      this.from_date = this.default_date_from;

      this.to_date = this.default_date_to;
    }

    this.time3 = [this.from_date, this.to_date];
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
  /*height: 45px !important;*/
  border: 1px solid #9e9e9e !important;
  color: black !important;
}
.mx-datepicker {
  width: 200px;
}

.mx-table-date td,
.mx-table-date th {
  text-align: center !important;
}
</style>
