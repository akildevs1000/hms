<template>
  <v-container>
    <v-row>
      <v-col cols="3">
        <AssetsPickerDateRange @months="CustomFilter" />
        <!-- <FilterDateRange  /> -->

      </v-col>
      <v-col cols="3">
        <v-autocomplete
          hide-details
          outlined
          dense
          :items="['All', 'Outstanding']"
          label="Select Status"
          v-model="statement_type"
          @change="CustomFilter"
        ></v-autocomplete>
      </v-col>
      <v-col cols="3" v-if="item && item.id">
        <v-autocomplete
          hide-details
          outlined
          dense
          :items="[{ id: ``, full_name: `Select All` }, ...item.customers]"
          item-value="id"
          item-text="full_name"
          label="Select Guest"
          v-model="customer_id"
          @change="CustomFilter"
        ></v-autocomplete>
      </v-col>
      <v-col cols="3" class="text-right">
        <AssetsIcon
          icon="printer-outline"
          @click="processFile(`statement-print`)"
        />
        &nbsp;
        <AssetsIcon
          icon="download-outline"
          @click="processFile(`statement-download`)"
        />
      </v-col>
    </v-row>
    <v-card class="pa-5 mt-2" elevation="0" v-if="dataLoaded">
      <v-row>
        <v-col cols="12" v-if="statements.length">
          <AssetsTable :headers="headers" :items="statements" />
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
const getFirstAndLastMonth = () => {
  const fullYear = new Date().getFullYear();
  return [`${fullYear}-01`, `${fullYear}-12`];
};
export default {
  props: ["source_id", "item"],
  data() {
    return {
      statement_type: "All",
      stats: [],
      customer: "",
      statements: [],
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      viewportWidth: 0,
      viewportHeight: 0,
      headers: [],
      months: getFirstAndLastMonth(),
      dummyData: [],
      company: null,
      dataLoaded: false,
      customer_id: 0,
    };
  },
  watch: {
    source_id() {
      this.get_source_history();
    },
  },
  computed: {
    formattedMonths() {
      if (!this.months || this.months.length !== 2) return "";

      // Split the months into year and month parts
      const [startYear, startMonth] = this.months[0].split("-");
      const [endYear, endMonth] = this.months[1].split("-");

      // Create start and end date objects
      const startDate = new Date(startYear, startMonth - 1, 1); // 1st day of the start month
      const endDate = new Date(endYear, endMonth, 0); // Last day of the end month

      // Options for formatting the dates
      const options = { day: "2-digit", month: "short", year: "numeric" };

      // Format the start and end dates
      const startFormatted = startDate.toLocaleDateString("en-US", options);
      const endFormatted = endDate.toLocaleDateString("en-US", options);

      // Return the formatted string
      return `${startFormatted} to ${endFormatted}`;
    },
  },

  mounted() {
    this.get_source_history();
    this.viewportWidth = window.innerWidth;
    this.viewportHeight = window.innerHeight;

    window.addEventListener("resize", () => {
      this.viewportWidth = window.innerWidth;
      this.viewportHeight = window.innerHeight;
    });
  },
  methods: {
    processFile(endpoint) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute(
        "href",
        `${process.env.BACKEND_URL}${endpoint}/${this.source_id}/${this.statement_type}/${this.months[0]}/${this.months[1]}`
      );
      document.body.appendChild(element);
      element.click();
    },
    CustomFilter(e) {
      this.months = e;
      this.get_source_history();
    },
    goToRevView(item) {},

    get_source_history() {
      this.dataLoaded = false;
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
          statement_type: this.statement_type,
          customer_id: this.customer_id || 0,
          from_date: this.months[0],
          to_date: this.months[1],
        },
      };
      this.$axios
        .get(`get_source_statement/${this.source_id}`, config)
        .then(({ data }) => {
          this.company = data.company;

          this.headers = [
            { text: `#`, value: `serial`, align: `left` },
            { text: `Date`, value: `date`, align: `left` },
            { text: `Transaction`, value: `transaction`, align: `left` },
            { text: `Description`, value: `description`, align: `left` },
            { text: `Amount`, value: `amount`, align: `right` },
            { text: `Payment`, value: `payment`, align: `right` },
            { text: `Balance`, value: `balance`, align: `right` },
          ];
          this.dataLoaded = true;

          let tempBalance = 0;

          this.statements = data.statementList.map((e, i) => {
            if (e.isOpeningBalance) {
              return {
                serial: i + 1,
                ...e,
                amount: this.$utils.currency_format(0),
                payment: this.$utils.currency_format(0),
                balance: this.$utils.currency_format(e.balance),
              };
            }

            let payment =
              e.is_city_ledger == 0
                ? i == 1
                  ? e.booking.transactions_sum_debit
                  : e.amount
                : 0;
            let amount =
              e.is_city_ledger == 1
                ? i == 1
                  ? e.booking.transactions_sum_debit
                  : e.amount
                : 0;

            tempBalance -= amount - payment; // Subtract amount from tempBalance for non-first city ledger entries

            return {
              serial: i + 1,
              date: e.date ? this.$dateFormat.dmy(e.date) : "---",
              transaction: e.is_city_ledger ? "Invoice" : e.description,
              description:
                e.booking_id < 1000 ? `INV-0000${e.booking_id}` : e.booking_id,
              amount: amount ? this.$utils.currency_format(amount) : "---",
              payment: payment ? this.$utils.currency_format(payment) : "---",
              balance: this.$utils.currency_format(Math.abs(tempBalance)),
            };
          });
        });
    },
  },
};
</script>
