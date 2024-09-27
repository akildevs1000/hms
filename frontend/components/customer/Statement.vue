<template>
  <v-container>
    <v-row>
      <v-col cols="4">
        <AssetsPickerMonthly @months="CustomFilter" />
      </v-col>
      <v-col cols="4">
        <v-select
          hide-details
          outlined
          dense
          :items="['All', 'Outstanding']"
          label="Select Status"
          v-model="statement_type"
          @change="CustomFilter"
        ></v-select>
      </v-col>
      <v-col cols="4" class="text-right">
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
    <v-card class="pa-5 mt-2" elevation="0" v-if="customer && customer.id">
      <v-row>
        <!-- <v-col cols="8">
          <v-img :src="company.logo" max-width="100" class="mb-3"></v-img>
          <div style="display: block">
            <h4><AssetsTextLabel label="To" /></h4>
            <AssetsTextLabel
              :label="`${customer.title} ${customer.full_name}`"
            />
            <br />
            <AssetsTextLabel :label="customer.country" />
            <AssetsTextLabel label="TRN 100437109000003" />
          </div>
        </v-col>

        <v-col cols="4" class="text-right">
          <h4>
            <AssetsTextLabel color="black" :label="`${company.name}`" />
          </h4>
          <AssetsTextLabel :label="`P.O.BOX: ${company.p_o_box_no}`" />
          <br />
          <AssetsTextLabel :label="`${company.location}`" />
          <br />
          <AssetsTextLabel label="TRN 100391471000003" />
          <br />

          <h4 class="mt-5">
            <AssetsTextLabel color="black" label="Statement of Accounts" />
          </h4>

          <span
            class=""
            style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd"
          >
            <AssetsTextLabel :label="formattedMonths" />
          </span>

          <v-row class="pb-5 pt-2">
            <v-col></v-col>
            <v-col cols="10">
              <table style="width: 100%" cellspacing="0">
                <tr>
                  <td colspan="2" class="text-left px-1 grey lighten-3">
                    <h4>
                      <AssetsTextLabel color="black" label="Account Summary" />
                    </h4>
                  </td>
                </tr>
                <tr>
                  <td class="text-left">
                    <AssetsTextLabel label="Opening Balance" />
                  </td>
                  <td><AssetsTextLabel label="AED 3,659.25" /></td>
                </tr>
                <tr>
                  <td class="text-left">
                    <AssetsTextLabel label="Invoiced Amount" />
                  </td>
                  <td>
                    <AssetsTextLabel label="AED 6,851.25" />
                  </td>
                </tr>
                <tr>
                  <td class="text-left">
                    <AssetsTextLabel label="Amount Received" />
                  </td>
                  <td>
                    <AssetsTextLabel label="AED 8,526.00" />
                  </td>
                </tr>
                <tr>
                  <td class="text-left" style="border-top: 1px solid #ddd">
                    <AssetsTextLabel label="Balance Due" />
                  </td>
                  <td style="border-top: 1px solid #ddd">
                    <AssetsTextLabel label="AED 1,984.50" />
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>
        </v-col> -->
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
  props: ["customerId"],
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
    };
  },
  watch: {
    customerId() {
      this.get_customer_history();
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
    this.get_customer_history();
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
        `${process.env.BACKEND_URL}${endpoint}/${this.customer.id}/${this.statement_type}/${this.months[0]}/${this.months[1]}`
      );
      document.body.appendChild(element);
      element.click();
    },
    CustomFilter(e) {
      this.months = e;
      this.get_customer_history();
    },
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    get_customer_history() {
      let config = {
        params: {
          statement_type: this.statement_type,
          from_date: this.months[0],
          to_date: this.months[1],
        },
      };
      this.$axios
        .get(`get_customer_statement/${this.customerId}`, config)
        .then(({ data }) => {
          this.customer = data.customer;
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
