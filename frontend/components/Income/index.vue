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
                  {{ item.date }}
                </td>
                <td class="text-center py-2 border-bottom">
                  {{ item.time }}
                </td>
                <td class="text-center py-2 border-bottom">
                  <span @click="goToRevView(item)" style="cursor: pointer">
                    {{ item.booking.reservation_no || "---" }}
                  </span>
                </td>
                <td class="text-center py-2 border-bottom">
                  {{ item.room || "---" }}
                </td>
                <td class="text-center py-2 border-bottom">
                  {{
                    item &&
                    item.booking &&
                    item.booking.customer &&
                    item.booking.customer.first_name
                  }}
                </td>
                <td class="text-left py-2 border-bottom">
                  {{ item.description }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.Cash) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.Card) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.Online) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.Bank) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.UPI) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.Cheque) }}
                </td>
                <td class="text-right py-2 border-bottom">
                  {{ $utils.currency_format(item.CityLedger) }}
                </td>
              </tr>
            </tbody>

            <tr>
              <td colspan="7" class="py-2 border-bottom">Total</td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.Cash) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.Card) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.Online) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.Bank) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.UPI) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.Cheque) }}
              </td>
              <td class="text-right py-2 border-bottom">
                {{ $utils.currency_format(incomeStats.CityLedger) }}
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
    total: 0,
    incomeHeaders: [
      { align: "center", text: "#" },
      { align: "center", text: "Date" },
      { align: "center", text: "Time" },
      { align: "center", text: "Rev. No" },
      { align: "center", text: "Rooms" },
      { align: "center", text: "Guest" },
      { align: "left", text: "Description" },
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
    incomeStats: {
      Cash: 0,
      Card: 0,
      Online: 0,
      Bank: 0,
      UPI: 0,
      Cheque: 0,
      CityLedger: 0,
    },
  }),
  created() {
    this.getData();
  },
  watch: {
    filters: {
      deep: true, // Deep watch for object changes
      handler(data) {
        this.from_date = data.from;
        this.to_date = data.to;
        this.search = data.search;
        if (this.from_date && this.to_date) {
          this.getData();
        }
      },
    },
  },
  computed: {},
  methods: {
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
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
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
        this.incomeStats = data.stats;
        this.$emit("stats", data.stats);
        this.loading = false;
      });
    },
  },
};
</script>
