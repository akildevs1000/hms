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
        <v-col cols="8"></v-col>
        <v-col cols="2">
          <v-text-field
            class="global-search-textbox"
            label="Search..."
            clearable
            dense
            outlined
            hide-details
            v-model="search"
            @input="getBySearch"
          ></v-text-field>
        </v-col>
        <v-col cols="2" class="text-right">
          <FilterDateRange height="30" @filter-attr="filterAttr" />
        </v-col>
        <v-col>
          <table cellspacing="0" style="width: 100%">
            <AssetsTableHeader :cols="incomeHeaders" />

            <tbody>
              <tr v-for="(item, index) in expenseData" :key="index">
                <td class="text-center py-2 border-bottom">
                  <small>{{ ++index }}</small>
                </td>
                <td class="text-center py-2 border-bottom">
                  <small>{{ item.expense.datetime }}</small>
                </td>
                <td class="text-center py-2 border-bottom">
                  <small>{{ item?.expense?.vendor?.first_name }}</small>
                </td>
                <td class="text-center py-2 border-bottom">
                  <small>{{
                    item?.expense?.vendor?.vendor_category?.name
                  }}</small>
                </td>

                <td class="text-center py-2 border-bottom">
                  <small>{{ item.detail }}</small>
                </td>
                <td class="text-center py-2 border-bottom">
                  <small>{{ item.qty }}</small>
                </td>

                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(item.Cash) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(item.Card) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(item.Online) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(item.Bank) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(item.UPI) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(item.Cheque) }}</small>
                </td>
              </tr>
              <tr>
                <td class="text-right py-2 border-bottom" colspan="6">
                  <small>Total</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(stats.Cash) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(stats.Card) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(stats.Online) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(stats.Bank) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(stats.UPI) }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small>{{ $utils.currency_format(stats.Cheque) }}</small>
                </td>
              </tr>
            </tbody>
          </table>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
export default {
  props: ["is_admin_expense"],
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
    options: {},
    endpoint: "expense-count",
    filterType: 1,
    search: "",
    snackbar: false,
    dialog: false,
    expenseData: [],
    stats: [],
    managementExpenseData: [],
    counts: [],
    loading: false,
    total: 0,
    incomeHeaders: [
      { align: "center", text: "#" },
      { align: "center", text: "DateTime" },
      { align: "center", text: "Vendor" },
      { align: "center", text: "Category" },
      { align: "center", text: "Item" },
      { align: "center", text: "Qty" },
      { align: "right", text: "Cash" },
      { align: "right", text: "Card" },
      { align: "right", text: "Online" },
      { align: "right", text: "Bank" },
      { align: "right", text: "UPI" },
      { align: "right", text: "Cheque" },
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
    this.getExpenseData();
  },
  computed: {},
  methods: {
    getBySearch() {
      if (
        !this.search ||
        this.search === null ||
        this.search.length === 0 ||
        this.search.length > 3
      ) {
        this.getExpenseData();
      }
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) {
        this.getExpenseData();
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
    getExpenseData() {
      if (this.loading) return false;
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          from_date: this.from_date,
          to_date: this.to_date,
          search: this.search,
          is_admin_expense: this.is_admin_expense,
        },
      };
      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.expenseData = data.data;
        this.stats = data.stats;
        this.$emit("stats", data.stats);
        this.loading = false;
      });
    },
  },
};
</script>
