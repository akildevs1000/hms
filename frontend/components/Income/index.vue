<template>
  <v-card class="mb-5 rounded-md mt-0" elevation="0">
    <!-- <v-toolbar
                    class="rounded-md"
                    color="background"
                    dense
                    flat
                    dark
                  >
                    <v-spacer></v-spacer>
                    <v-tooltip top color="primary">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="ma-0"
                          x-small
                          :ripple="false"
                          text
                          v-bind="attrs"
                          v-on="on"
                          @click="process('income_report_print')"
                        >
                          <v-icon class="">mdi-printer-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>PRINT</span>
                    </v-tooltip>

                    <v-tooltip top color="primary">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          x-small
                          :ripple="false"
                          text
                          v-bind="attrs"
                          v-on="on"
                          @click="process('income_report_download')"
                        >
                          <v-icon class="">mdi-download-outline</v-icon>
                        </v-btn>
                      </template>
                      <span> DOWNLOAD </span>
                    </v-tooltip>
                  </v-toolbar> -->
    <v-container fluid>
      <v-row dense>
        <v-col cols="12">
          <table cellspacing="0" style="width: 100%">
            <AssetsTableHeader :cols="incomeHeaders" />
            <tbody>
              <tr v-for="(item, index) in incomeData" :key="index">
                <td class="text-center py-2 border-bottom">
                  {{ index + 1 }}
                </td>
                <td class="text-center py-2 border-bottom">
                  {{ item.booking_date }}
                </td>
                <td class="text-center py-2 border-bottom">
                  <span @click="goToRevView(item)" style="cursor: pointer">
                    {{ item?.reservation_no || "---" }}
                  </span>
                </td>
                <td class="text-center py-2 border-bottom">
                  {{
                    Array.isArray(item?.order_rooms) && item?.order_rooms.length
                      ? item?.order_rooms.map((e) => e.room_no).join(", ")
                      : "---"
                  }}
                </td>

                <td class="text-center py-2 border-bottom">
                  {{ item && item?.customer && item.customer?.first_name }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.cash_sum_amount || 0) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.card_sum_amount || 0) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.online_sum_amount || 0) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.bank_sum_amount || 0) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.upi_sum_amount || 0) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.cheque_sum_amount || 0) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item?.pending_sum_amount || 0) }}
                </td>
              </tr>
            </tbody>

            <tr v-if="allTotalProcessed">
              <td colspan="5" class="py-2 border-bottom">Total</td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.cash) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.card) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.online) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.bank) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.upi) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.cheque) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(totals.pending) }}
              </td>
            </tr>
          </table>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
export default {
  props: ["filters"],
  data: () => ({
    Model: "Expense",
    vertical: false,
    activeTab: 0,
    from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    from_menu: false,
    to_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    to_menu: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
      status: "-1",
    },
    options: {},
    endpoint: "payments",
    search: "",
    snackbar: false,
    dialog: false,
    expenseData: [],
    incomeData: [],
    counts: [],
    loading: false,
    incomeHeaders: [
      { align: "center", text: "#" },
      { align: "center", text: "Date" },
      { align: "center", text: "Rev. No" },
      { align: "center", text: "Rooms" },
      { align: "center", text: "Guest" },
      { align: "right", text: "Cash" },
      { align: "right", text: "Card" },
      { align: "right", text: "Online" },
      { align: "right", text: "Bank" },
      { align: "right", text: "UPI" },
      { align: "right", text: "Cheque" },
      { align: "right", text: "City Ledger" },
    ],
    editedIndex: -1,
    response: "",
    loss: "",
    profit: "",
    errors: [],
    search: null,
    editedItem: {
      item: null,
      amount: null,
      payment_modes: "CASH",
    },
    totals: {
      cash: 0,
      card: 0,
      online: 0,
      bank: 0,
      upi: 0,
      cheque: 0,
      pending: 0,
    },
    allTotalProcessed: false,
  }),
  created() {
    this.getData();
  },
  watch: {
    filters: {
      handler(data) {
        this.from_date = data.from;
        this.to_date = data.to;
        this.search = data.search;
        if (this.from_date && this.to_date) {
          this.getData();
        }
      },
      deep: true,
      immediate: true, // Optional: triggers the watcher immediately on component mount
    },
  },
  computed: {},
  methods: {
    getTotalCash(data) {
      this.allTotalProcessed = false;

      const totals = {
        cash: 0,
        card: 0,
        online: 0,
        bank: 0,
        upi: 0,
        cheque: 0,
        pending: 0,
      };

      for (const item of data) {
        totals.cash += Number(item?.cash_sum_amount) || 0;
        totals.card += Number(item?.card_sum_amount) || 0;
        totals.online += Number(item?.online_sum_amount) || 0;
        totals.bank += Number(item?.bank_sum_amount) || 0;
        totals.upi += Number(item?.upi_sum_amount) || 0;
        totals.cheque += Number(item?.cheque_sum_amount) || 0;
        totals.pending += Number(item?.pending_sum_amount) || 0;
      }

      this.totals = totals;
      this.allTotalProcessed = true;

      this.$emit("stats", totals);
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    goToRevView(item) {
      // this.$router.push(`/customer/details/${item.booking.id}`);
    },
    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let url = `${process.env.BACKEND_URL}${type}?company_id=${comId}&from=${from}&to=${to}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },
    incomeByPaymentMode(item, paymentModeKey) {
      let payments = item.payments;
      payments.forEach((e) => {
        const paymentMode = (e?.payment_mode?.name ?? "Cash").replace(" ", "");
        if (paymentMode === paymentModeKey) {
          console.log("🚀 ~ payments.forEach ~ paymentMode:", paymentMode);
          console.log("🚀 ~ payments.forEach ~ e:", parseFloat(e?.amount || 0));
          return parseFloat(e?.amount || 0);
        } else {
          return 0;
        }
      });
    },
    getData() {
      if (this.loading) return false;
      this.loading = true;
      let options = {
        params: {
          page: this.pagination.current,
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          from_date: this.from_date,
          to_date: this.to_date,
          search: this.search,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.incomeData = data.data;

        this.getTotalCash(data.data);
        this.loading = false;
      });
    },
  },
};
</script>
