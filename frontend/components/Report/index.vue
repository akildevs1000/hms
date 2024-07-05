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
      <FilterCustomFilter @filter-attr="filterAttr" :defaultFilterType="1" />
      <!-- <pre>
        {{ filters }}
      </pre> -->
    </v-col>
    <v-col cols="12">
      <component
        :key="compKey"
        :is="currentComponent"
        v-if="currentComponent"
        :data="tableData.data"
        :colors="tableData.colors"
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
      compKey: 0,
      loading: true,
      filters: {
        report_type: "source",
        date: null,
      },
      tableData: [],

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
    // this.loading = false;
    this.getData();
  },
  computed: {
    currentComponent() {
      const componentMap = {
        source: "ReportSource",
        payment: "ReportPayment",
        room: "ReportRoom",
        days: "ReportDay",
        income: "ReportIncome",
        walkin: "ReportWalkin",
      };
      return componentMap[this.filters.report_type];
    },
  },
  methods: {
    filterAttr(data) {
      this.filters.filter_from_date = data.from;
      this.filters.filter_to_date = data.to;

      if (this.filters.filter_from_date && this.filters.filter_to_date) {
        this.getData();
      }
    },
    getData() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
          filter_from_date: this.filters.filter_from_date,
          filter_to_date: this.filters.filter_to_date,
          company_id: this.$auth.user.company_id,
        },
      };
      let Options = {
        source: this.getSourceData(config),
        payment: this.getPaymentData(config),
        room: this.getRoomData(config),
        days: this.getDaysData(config),
        income: this.getIncomeData(config),
        walkin: this.getWalkinData(config),
      };
      Options[this.filters.report_type];
    },

    async getSourceData(config) {
      let { data } = await this.$axios.get(`https://backend.myhotel2cloud.com/api/report-by-source?company_id=1`, config);
      this.tableData = data;
      this.compKey++;
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
