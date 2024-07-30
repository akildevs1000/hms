<template>
  <v-card v-if="can(`accounts_gst_access`)" style="background: none">
    <v-data-table
      dense
      :headers="headers"
      :items="vendorCategories"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
      class="elevation-1 pa-3"
    >
      <template v-slot:top>
        <v-toolbar flat dense class="mb-5">
          {{ Model }}
          <v-icon color="primary" right class="mt-1" @click="getDataFromApi()"
            >mdi-reload</v-icon
          >
          <v-spacer></v-spacer>
        </v-toolbar>
      </template>
      <template v-slot:item.show_taxable_invoice_number="{ item }">
        {{ item.show_taxable_invoice_number || "---" }}
      </template>

      <template v-slot:item.reservation_no="{ item }">
        {{ item.reservation_no || "---" }}
      </template>

      <template v-slot:item.gst="{ item }">
        {{
          (item &&
            item.booking &&
            item.booking.customer &&
            item.booking.customer.gst_number) ||
          "---"
        }}
      </template>
      <template v-slot:item.source="{ item }">
        {{ (item && item.booking && item.booking.source) || "---" }}
      </template>

      <template v-slot:item.customer="{ item }">
        {{
          (item &&
            item.booking &&
            item.booking.customer &&
            item.booking.customer.full_name) ||
          "---"
        }}
      </template>
      <template v-slot:item.arrival_date="{ item }">
        {{ item && item.booking && item.booking.check_in }}
      </template>

      <template v-slot:item.departure_date="{ item }">
        {{ item && item.booking && item.booking.check_out }}
      </template>

      <template v-slot:item.total_invoice="{ item }">
        {{ (item && item.booking && item.booking.total_price) || "0" }}
      </template>

      <template v-slot:item.posting="{ item }">
        {{ (item && item.booking && item.booking.total_posting_amount) || 0 }}
      </template>

      <template v-slot:item.paid_amount="{ item }">
        {{ (item && item.booking && item.booking.paid_amounts) || 0 }}
      </template>

      <template v-slot:item.balance="{ item }">
        {{ (item && item.booking && item.booking.balance) || 0 }}
      </template>

      <template v-slot:item.gst_amount="{ item }">
        {{
          (item && item.booking && item.booking.inv_total_tax_collected) || 0
        }}
      </template>

      <template v-slot:item.booking_date="{ item }">
        {{ (item && item.booking && item.booking.booking_date) || "---" }}
      </template>

      <template v-slot:item.invoice="{ item }">
        <v-icon
          @click="
            redirect_to_invoice(
              item && item.booking && item.booking.id,
              item.show_taxable_invoice_number
            )
          "
          x-small
          color="primary"
          class="mr-2"
        >
          mdi-cash-multiple
        </v-icon>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    Model: "Taxables",
    endpoint: "taxable_invoice",
    filters: {},
    options: {},
    loading: false,
    response: "",
    vendorCategories: [],
    errors: [],
    headers: [
      { text: "Invoice No", value: "show_taxable_invoice_number" },
      { text: "Resr. No", value: "reservation_no" },
      { text: "GST", value: "gst" },
      { text: "Source", value: "source" },
      { text: "Customer", value: "customer" },
      { text: "Arrival  Date", value: "arrival_date" },
      { text: "Departure  Date", value: "departure_date" },
      { text: "Total(inv)", value: "total_invoice" },
      { text: "Posting", value: "posting" },
      { text: "Paid Amount", value: "paid_amount" },
      { text: "Balance", value: "balance" },
      { text: "GST Amt", value: "gst_amount" },
      { text: "Booking Date", value: "booking_date" },
      { text: "Invoice", value: "invoice" },
    ],
    componentKey: 1,
  }),

  async created() {
    this.getDataFromApi();
  },
  mounted() {},
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return ((u && u.permissions.some(e => e == per || per == "/")) || u.is_master);
    },
    convert_date_format(val) {
      if (!val)
        return "---";
      const date = new Date(val);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return [year, month, day].join("-");
    },
    async getDataFromApi() {
      this.loading = true;
      let { data } = await this.$axios.get(this.endpoint);
      this.loading = false;
      this.vendorCategories = data.data;
    },
  },
};
</script>
