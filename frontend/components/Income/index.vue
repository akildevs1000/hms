<template>
     <v-card class="mb-5 rounded-md mt-3" elevation="0">
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
            <table>
          <tr>
            <td v-for="(item, index) in incomeHeaders" :key="index">
              <span v-html="item.text"></span>
            </td>
          </tr>
          <v-progress-linear
            v-if="loading"
            :active="loading"
            :indeterminate="loading"
            absolute
            color="primary"
          ></v-progress-linear>

          <tr v-for="(item, index) in incomeData" :key="index">
            <td>{{ ++index }}</td>
            <td>{{ item.date }}</td>
            <td>{{ item.time }}</td>
            <td>
              <span
                class="blue--text"
                @click="goToRevView(item)"
                style="cursor: pointer"
              >
                {{ item.booking.reservation_no || "---" }}
              </span>
            </td>
            <td style="max-width: 20px !important">
              {{ item.room }}
            </td>
            <td>
              {{
                item &&
                item.booking &&
                item.booking.customer &&
                item.booking.customer.first_name
              }}
            </td>
            <td>{{ item.description }}</td>

            <td v-for="i in 7" :key="i" class="text-right">
              <span
                v-if="
                  (item && item.payment_mode && item.payment_mode.name) ==
                    'Cash' && i == 1
                "
              >
                {{ item.amount }}
              </span>
              <span
                v-else-if="(item && item.payment_mode.name) == 'Bank' && i == 4"
              >
                {{ item.amount }}
              </span>
              <span
                v-else-if="
                  (item && item.payment_mode.name) == 'Online' && i == 3
                "
              >
                {{ item.amount }}
              </span>
              <span
                v-else-if="(item && item.payment_mode.name) == 'UPI' && i == 5"
              >
                {{ item.amount }}
              </span>
              <span
                v-else-if="(item && item.payment_mode.name) == 'Card' && i == 2"
              >
                {{ item.amount }}
              </span>

              <span
                v-else-if="
                  (item && item.payment_mode.name) == 'City Ledger' && i == 7
                "
              >
                {{ item.amount }}
              </span>

              <span v-else> --- </span>
            </td>
          </tr>
          <tr class="text-right">
            <td colspan="7">Total</td>
            <td>{{ totalIncomes.Cash }}</td>
            <td>{{ totalIncomes.Card }}</td>
            <td>{{ totalIncomes.Online }}</td>
            <td>{{ totalIncomes.Bank }}</td>
            <td>{{ totalIncomes.UPI }}</td>
            <td>{{ totalIncomes.Cheque }}</td>
            <td>{{ totalIncomes.City_ledger }}</td>
          </tr>
        </table>
        </v-container>
      </v-card>
</template>

<script>
export default {
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
    filterType: 1,
    search: "",
    snackbar: false,
    dialog: false,
    expenseData: [],
    managementExpenseData: [],
    incomeData: [],
    counts: [],
    loading: false,
    total: 0,
    incomeHeaders: [
      { text: "#" },
      { text: "Date" },
      { text: "Time" },
      { text: "Rev. No" },
      { text: "Rooms" },
      { text: "Guest" },
      { text: "Description" },
      { text: "Cash" },
      { text: "Card" },
      { text: "Online" },
      { text: "Bank" },
      { text: "UPI" },
      { text: "Cheque" },
      { text: "City Ledger" },
    ],
    editedIndex: -1,
    response: "",
    loss: "",
    profit: "",
    errors: [],
    editedItem: {
      item: null,
      amount: null,
      payment_modes: "CASH",
    },
    totalExpenses: {},
    managementExpense: {},
    totalIncomes: {},
  }),
  created() {
    this.loading = true;
    this.getIncomeData();
  },
  computed: {},
  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) {
        this.getIncomeData();
      }
    },
    getPriceFormat(price) {
      return (
        this.$auth.user.company.currency +
        " " +
        parseFloat(price).toLocaleString("en-IN", {
          maximumFractionDigits: 2,
        })
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.booking.id}`);
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
    getIncomeData() {
      this.loading = true;
      let options = {
        params: {
          page: this.pagination.current,
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          from_date: this.from_date,
          to_date: this.to_date,
        },
      };
      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.incomeData = data;
      this.loading = false;

      });
    },
  },
};
</script>

<style scoped>
@import url("../../assets/css/tableStyles.css");
</style>
