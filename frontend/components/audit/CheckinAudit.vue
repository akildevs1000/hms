<template>
  <div>
    <v-col md="12">
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
          <label class="white--text">Today Checkin Report</label>
          <v-spacer></v-spacer>
          <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }">
              <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                @click="process('income_report_print')">
                <v-icon class="">mdi-printer-outline</v-icon>
              </v-btn>
            </template>
            <span>PRINT</span>
          </v-tooltip>

          <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }">
              <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="process('income_report_download')">
                <v-icon class="">mdi-download-outline</v-icon>
              </v-btn>
            </template>
            <span> DOWNLOAD </span>
          </v-tooltip>
        </v-toolbar>
        <table>
          <tr>
            <th v-for="(item, index) in incomeHeaders" :key="index">
              <span v-html="item.text"></span>
            </th>
          </tr>
          <tr v-for="(item, index) in todayCheckIn" :key="index" style="background-color: yellow">
            <td>{{ ++index }}</td>
            <td>{{ item && item.customer && item.customer.first_name }}</td>
            <td>
              <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer">
                {{ item.rooms }}
              </span>
            </td>
            <td>{{ item && item.source }}</td>
            <td>{{ item && item.check_in }}</td>
            <td>{{ item && item.check_out }}</td>
            <td class="text-right">{{ item.total_price }}</td>
            <td class="text-right">{{ item.advance_price }}</td>
            <td class="text-right">{{ getPaymentMode(item, 1) }}</td>
            <td class="text-right">{{ getPaymentMode(item, 2) }}</td>
            <td class="text-right">{{ getPaymentMode(item, 3) }}</td>
            <td class="text-right">{{ getPaymentMode(item, 4) }}</td>
            <td class="text-right">{{ getPaymentMode(item, 5) }}</td>
            <td class="text-right">
              {{ item.balance }}
            </td>
            <td>
              {{ item.balance > 0 ? "Payment pending" : "Payment close" }}
            </td>
          </tr>
          <tr class="text-right">
            <th class="text-right" colspan="8">Total</th>
            <th class="text-right">{{ totalCash }}</th>
            <th class="text-right">{{ totalCard }}</th>
            <th class="text-right">{{ totalOnline }}</th>
            <th class="text-right">{{ totalBank }}</th>
            <th class="text-right">{{ totalUPI }}</th>
            <th class="text-right">{{ totalBalance }}</th>
          </tr>
        </table>
      </v-card>
    </v-col>
  </div>
</template>

<script>
export default {
  props: ['todayCheckIn'],
  data: () => ({
    Model: "Audit Report",
    from_date: new Date().toJSON().slice(0, 10),
    from_menu: false,
    options: {},
    endpoint: "expense",
    search: "",
    snackbar: false,
    dialog: false,
    todayCheckIn: [],
    todayCheckOut: [],
    continueRooms: [],
    todayPayments: [],
    counts: [],
    loading: false,
    total: 0,
    totExpense: 0,

    incomeHeaders: [
      { text: "#" },
      { text: "Guest" },
      { text: "Rooms" },
      { text: "Source" },
      { text: "CheckIn" },
      { text: "CheckOut" },
      { text: "Tariff" },
      { text: "Advance" },
      { text: "Cash" },
      { text: "Card" },
      { text: "Online" },
      { text: "Bank" },
      { text: "UPI" },
      { text: "Balance" },
      { text: "Remark" },
    ],
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
  }),

  created() {
    this.loading = true;
  },

  computed: {
  },

  methods: {

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    getPaymentMode(item, mode) {
      let creditTrans = item.transactions.filter((e) => e.credit > 0);
      switch (mode) {
        case 1:
          return this.getPaySum(creditTrans, 1)
        case 2:
          return this.getPaySum(creditTrans, 2)
        case 3:
          return this.getPaySum(creditTrans, 3)
        case 4:
          return this.getPaySum(creditTrans, 4)
        case 5:
          return this.getPaySum(creditTrans, 5)
        default:
          break;
      }
    },

    getPaySum(payload, mode) {
      let sum = 0;
      payload.map((e) => {
        if (e.payment_method_id == mode) {
          sum += parseFloat(e.credit)
        }
      });
      return sum.toFixed(2);
    },

    commonMethod() {
      this.getdata();
    },



  },
};
</script>