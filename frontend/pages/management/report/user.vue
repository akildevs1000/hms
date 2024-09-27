<template>
  <div v-if="can('management_income_access') && can('management_income_view')">
    <v-row class="mt-0 mb-0">
      <v-col cols="6">
        <h3>Payments</h3>

      </v-col>
      <v-col cols="6">
        <v-spacer></v-spacer>

      </v-col>
    </v-row>
    <v-row>
      <div class="col-xl-2 my-0 py-0 col-lg-2 text-uppercase" v-for="(user, index) in  paymentReportsByUser "
        :key="index">
        <div class="card px-2" :style="{ backgroundColor: colors[index] || '#3366CC' }">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">{{ user.name }}</h6>
              <span class="data-1"> {{ getUserTotal(user) }}</span>
            </div>
          </div>
        </div>
      </div>
    </v-row>
    <v-row>
      <!-- <v-col md="2">

        <v-select v-model="filterType" :items="[
          {
            id: 1,
            name: 'Today',
          },
          {
            id: 2,
            name: 'Yesterday',
          },
          {
            id: 3,
            name: 'This Week',
          },
          {
            id: 4,
            name: 'This Month',
          },
          {
            id: 5,
            name: 'Custom',
          },
        ]
          " dense placeholder="Type" outlined :hide-details="true" item-text="name" item-value="id"
          @change="commonMethod"></v-select>

      </v-col>
      <v-col md="2" class="p-2 text-center" v-if="filterType == 5">
        <DateRangePicker :disabled="false" key="taxable" :DPStart_date="from_date" :DPEnd_date="to_date"
          column="date_range" @selected-dates="handleDatesFilter" />
      </v-col> -->


      <!-- <v-col md="3" v-if="filterType == 5">
        <div class="ml-4">From</div>
        <v-col cols="12" sm="12" md="12">
          <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
            offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" outlined dense
                :hide-details="true"></v-text-field>
            </template>
            <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
          </v-menu>
        </v-col>
      </v-col>
      <v-col md="3" v-if="filterType == 5">
        <div class="ml-4">To</div>
        <v-col cols="12" sm="12" md="12">
          <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
            offset-y min-width="auto">
            <template v-slot:activator="{ on, attrs }">
              <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" outlined dense
                :hide-details="true"></v-text-field>
            </template>
            <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
          </v-menu>
        </v-col>
      </v-col> -->
      <v-col xs="12" sm="12" md="4" cols="12">
        <CustomFilter @filter-attr="filterAttr" :defaultFilterType="1" />

      </v-col>
      <v-col md="3" v-if="userList.length > 0">
        <div class="ml-4">Users</div>
        <v-col cols="12" sm="12" md="12">
          <v-select @change="getPaymentReportsByUser()" v-model="user_id" :items="userList" item-text="name"
            item-value="id" outlined dense placeholder="User List" :hide-details="true" flat></v-select>
        </v-col>
      </v-col>
    </v-row>

    <v-row class="mt-0 mt-0">
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0" v-for="( user, index ) in  paymentReportsByUser " :key="index">
          <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
            <label class="white--text">{{ user.name }}</label>
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
              <th v-for="( item, index ) in  Header " :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
              color="primary"></v-progress-linear>

            <tr v-for="( trans, index ) in  user.transactions " :key="index">
              <td>{{ ++index }}</td>
              <td>
                <span class="blue--text" @click="goToRevView(trans)" style="cursor: pointer">
                  {{
                    (trans && trans.booking && trans.booking.reservation_no) ||
                    "---"
                  }}
                </span>
              </td>
              <td>
                {{ (trans && trans.booking && trans.booking.rooms) || "---" }}
              </td>
              <td>{{ trans.desc }}</td>
              <td>{{ trans.created_at }}</td>
              <td>{{ trans.time }}</td>
              <td>{{ trans.credit }}</td>
              <td>{{ trans.debit }}</td>
              <td v-for=" i  in  7 " :key="i" class="text-right">
                <span v-if="(trans && trans.payment_mode && trans.payment_mode.name) ==
                  'Cash' && i == 1
                  ">
                  {{ trans.credit }}
                </span>
                <span v-else-if="(trans && trans.payment_mode && trans.payment_mode.name) ==
                  'Card' && i == 2
                  ">
                  {{ trans.credit }}
                </span>
                <span v-else-if="(trans && trans.payment_mode && trans.payment_mode.name) ==
                  'Online' && i == 3
                  ">
                  {{ trans.credit }}
                </span>
                <span v-else-if="(trans && trans.payment_mode && trans.payment_mode.name) ==
                  'Bank' && i == 4
                  ">
                  {{ trans.credit }}
                </span>

                <span v-else-if="(trans && trans.payment_mode && trans.payment_mode.name) ==
                  'UPI' && i == 5
                  ">
                  {{ trans.credit }}
                </span>

                <span v-else-if="(trans && trans.payment_mode && trans.payment_mode.name) ==
                  'City Ledger' && i == 7
                  ">
                  {{ trans.debit }}
                </span>

                <span v-else> --- </span>
              </td>
            </tr>
            <tr class="text-right">
              <th colspan="8">Total</th>
              <th>{{ user.cash_sum }}</th>
              <th>{{ user.card_sum }}</th>
              <th>{{ user.online_sum }}</th>
              <th>{{ user.bank_sum }}</th>
              <th>{{ user.UPI_sum }}</th>
              <th>{{ user.cheque_sum }}</th>
              <th>{{ user.City_ledger_sum }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
import CustomFilter from '../../../components/filter/CustomFilter.vue';

export default {
  data: () => ({
    Model: "Expense",
    from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    from_menu: false,
    to_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
    to_menu: false,
    filterType: 1,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
      status: "-1",
    },
    options: {},
    endpoint: "expense",
    search: "",
    snackbar: false,
    dialog: false,
    expenseData: [],
    incomeData: [],
    counts: [],
    loading: false,
    total: 0,
    Header: [
      { text: "#" },
      { text: "Rev. No." },
      { text: "Rooms" },
      { text: "Desc." },
      { text: "Date" },
      { text: "Time" },
      { text: "Credit" },
      { text: "Debit" },
      { text: "Cash" },
      { text: "Card" },
      { text: "Online" },
      { text: "Bank" },
      { text: "UPI" },
      { text: "Cheque" },
      { text: "City Ledger" },
    ],
    colors: [
      "#3366CC",
      "#FF69B4",
      "#FF4500",
      "#800080",
      "#FF6347",
      "#008080",
      "#FFA500",
      "#DC143C",
      "#4169E1",
      "#3366CC",
      "#3366CC",
      "#FF69B4",
      "#FF4500",
      "#800080",
      "#FF6347",
      "#008080",
      "#FFA500",
      "#DC143C",
      "#4169E1",
      "#3366CC",
    ],
    editedIndex: -1,
    response: "",
    loss: "",
    profit: "",
    errors: [],
    userList: [],
    user_id: "",
    paymentReportsByUser: [],
    totalExpenses: {},
    totalIncomes: {},
  }),
  created() {
    this.loading = true;
    if (this.$auth.user.user_type != "employee") {
      this.get_users();
    }
    else {
      this.user_id = this.$auth.user.id;
    }
    this.getPaymentReportsByUser();
  },
  computed: {
    currentDate() {
      return new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },
    week() {
      const today = new Date();
      const dayOfWeek = today.getDay(); // Sunday = 0, Monday = 1, ..., Saturday = 6
      const startOfWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - dayOfWeek);
      const endOfWeek = new Date(today.getFullYear(), today.getMonth(), startOfWeek.getDate() + 6);
      return [
        startOfWeek.toISOString().slice(0, 10),
        endOfWeek.toISOString().slice(0, 10),
      ];
    },
  },
  methods: {
    getUserTotal(user) {
      return this.getPriceFormat(parseFloat(user.cash_sum) + parseFloat(user.card_sum) + parseFloat(user.online_sum) + parseFloat(user.bank_sum) + parseFloat(user.UPI_sum) + parseFloat(user.cheque_sum) + parseFloat(user.City_ledger_sum));
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      // this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) {
        if (this.$auth.user.user_type != "employee") {
          this.get_users();
        }
        else {
          this.user_id = this.$auth.user.id;
        }
        this.getPaymentReportsByUser();
      }
    },
    handleDatesFilter(dates) {
      this.from_date = dates[0];
      this.to_date = dates[1];
      if (this.from_date && this.to_date) {
        if (this.$auth.user.user_type != "employee") {
          this.get_users();
        }
        else {
          this.user_id = this.$auth.user.id;
        }
        this.getPaymentReportsByUser();
      }
    },
    getPriceFormat(price) {
      return this.$auth.user.company.currency + " " + parseFloat(price).toLocaleString('en-IN', {
        maximumFractionDigits: 2,
      });
    },
    formatDateTime(dateString) {
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      const seconds = String(date.getSeconds()).padStart(2, '0');
      return `${year}-${month}-${day} ${hours}:${minutes}`;
    },
    onPageChange() {
      this.getExpenseData();
    },
    can(per) {
      let u = this.$auth.user;
      return ((u && u.permissions.some(e => e == per || per == "/")) || u.is_master);
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      }
      else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      }
      else {
        return n + ".00";
      }
    },
    onPageChange() {
      this.getExpenseData();
    },
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.booking_id}`);
    },
    // getFirstAndLastDay() {
    //   const currentDate = new Date();
    //   const day = currentDate.getDate();
    //   const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
    //   const year = currentDate.getFullYear();
    //   const last = new Date(year, month, 0).getDate().toString().padStart(2, "0");
    //   let firstDay = `${year}-${month}-0${1}`;
    //   let lastDay = `${year}-${month}-0${last}`;
    //   return [
    //     firstDay,
    //     lastDay
    //   ]
    // },
    getFirstAndLastDay() {
      const currentDate = new Date();
      const day = currentDate.getDate();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
      const year = currentDate.getFullYear();
      const last = new Date(year, month, 0).getDate().toString().padStart(2, "0");
      let firstDay = `${year}-${month}-0${1}`;
      let lastDayFirst = last > 9 ? `${last}` : `0${last}`;
      console.log('date' + last);
      let lastDay = `${year}-${month}-${lastDayFirst}`;
      return [
        firstDay,
        lastDay
      ];
    },
    commonMethod() {
      // alert('ff');
      const today = new Date();
      console.log(this.currentDate);
      switch (this.filterType) {
        case 1:
          this.from_date = this.currentDate;
          this.to_date = this.currentDate;
          break;
        case 2:
          this.from_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);
          this.to_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);
          break;
        case 3:
          this.from_date = this.week[0];
          this.to_date = this.week[1];
          break;
        case 4:
          this.from_date = this.getFirstAndLastDay()[0];
          this.to_date = this.getFirstAndLastDay()[1];
          break;
        // default:
        //   this.from_date = new Date().toJSON().slice(0, 10);
        //   this.to_date = new Date().toJSON().slice(0, 10);
        //   break;
      }
      // console.log(this.from_date + "from");
      // console.log(this.to_date + "to");
      // console.log(this.filterType + "type");
      this.getPaymentReportsByUser();
    },
    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let url = process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },
    get_users() {
      console.log(this.$auth.user.user_type);
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          // user_type: this.$auth.user.user_type,
          // id: this.$auth.user.id,
        },
      };
      this.$axios.get(`users`, payload).then(({ data }) => {
        this.userList = data;
        this.userList.unshift({ id: "", name: "Select All" });
      });
    },
    getPaymentReportsByUser(url = "get_transaction_by_users") {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          user_type: this.$auth.user.user_type,
          user_id: this.user_id,
          from_date: this.from_date,
          to_date: this.to_date,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.paymentReportsByUser = data;
        this.loading = false;
      });
    },
  },
  components: { CustomFilter }
};
</script>