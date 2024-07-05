<template>
  <v-row>
    <v-col cols="6"> {{ heading }} </v-col>
    <v-col cols="3">
      <v-autocomplete
        v-model="filters.report_type"
        :items="report_types"
        label="Select Report Type"
        item-text="text"
        item-value="value"
        outlined
        dense
        class="red-text"
        @change="getData"
      ></v-autocomplete>
    </v-col>
    <v-col cols="3">
      <v-autocomplete
        v-model="filters.date"
        :items="dateOptions"
        label="Select Date"
        item-text="text"
        item-value="value"
        outlined
        dense
        class="red-text"
      ></v-autocomplete>
    </v-col>
    <v-col cols="12">
      <component
        :is="currentComponent"
        v-if="currentComponent"
        :tableData="tableData"
      ></component>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    heading: {
      default: "Reports",
    },
  },

  data() {
    return {
      loading: true,
      filters: {
        report_type: 'source',
        date: null,
        tableData: [],
      },
      report_types: [
        { text: "Revenue Source", value: "source" },
        { text: "Payment", value: "payment" },
        { text: "Room category", value: "room" },
        { text: "Week Days", value: "days" },
        { text: "Income", value: "income" },
        { text: "Walkin", value: "walkin" },
      ],
      dateOptions: [
        { text: "Today", value: "today" },
        { text: "Yesterday", value: "yesterday" },
        { text: "This week", value: "this_week" },
        { text: "This month", value: "this_month" },
        { text: "Customized date", value: "customized_date" },
      ],
    };
  },
  mounted() {
    // Prepare data for chart
    let data = [
      {
        color: "red",
        source: "Walking",
        no_of_room: 134,
        percentage: "45.00%",
        revenue: 450000.0,
      },
    ];
    this.tableData = data;
    this.loading = false;
  },
  computed: {
    currentComponent() {
      const componentMap = {
        source: 'ReportSource',
        payment: 'ReportPayment',
        room: 'ReportRoom',
        days: 'ReportDay',
        income: 'ReportIncome',
        walkin: 'ReportWalkin'
      };
      return componentMap[this.filters.report_type];
    }
  },
  methods: {
    getData() {
      let Options = {
        source: this.getSourceData(),
        payment: this.getPaymentData(),
        room: this.getRoomData(),
        days: this.getDaysData(),
        income: this.getIncomeData(),
        walkin: this.getWalkinData(),
      };
      this.tableData = Options[this.filters.report_type];
    },

    getSourceData() {
      return [
        {
          color: "red",
          source: "Walking",
          no_of_room: 134,
          percentage: "45.00%",
          revenue: 450000.0,
        },
      ];
    },
    getPaymentData() {
      return [
        {
          color: "red",
          source: "Cash",
          no_of_room: 134,
          percentage: "45.00%",
          revenue: 450000.0,
        },
      ];
    },
    getRoomData() {
      return [
        {
          color: "red",
          source: "King",
          no_of_room: 134,
          percentage: "45.00%",
          revenue: 450000.0,
        },
      ];
    },
    getDaysData() {
      return [
        {
          color: "red",
          source: "Monday",
          no_of_room: 134,
          percentage: "45.00%",
          revenue: 450000.0,
        },
      ];
    },
    getIncomeData() {
      return [
        {
          color: "red",
          source: "Jan 23",
          no_of_room: 134,
          percentage: "45.00%",
          revenue: 450000.0,
        },
      ];
    },
    getWalkinData() {
      return [
        {
          color: "red",
          source: "Saravan",
          no_of_room: 134,
          percentage: "45.00%",
          revenue: 450000.0,
        },
      ];
    },
  },
};
</script>
