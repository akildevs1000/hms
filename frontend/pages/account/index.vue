<template>
  <div v-if="can('management_income_access') && can('management_income_view')">
    <v-row>
      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Income</h6>
              <span class="data-1">
                {{ getPriceFormat(totalIncomes.OverallTotal) || 0 }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Expense</h6>
              <span class="data-1"> {{ getPriceFormat(totalExpenses.OverallTotal) || "0.00" }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase" v-if="can('management_income_view')">
        <div class="card px-2" style="background-color: #ce008e">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Management Expense</h6>
              <span class="data-1"> {{ getPriceFormat(totalExpenses.ManagementOverallTotal) || 0 }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase" v-if="can('management_income_view')">
        <div class="card px-2 checkedIn">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-doosr-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Profit</h6>
              <!-- <span class="data-1"> RS. {{ profit }}</span> -->
              <span class="data-1"> {{ getPriceFormat(profit) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase">
        <div class="card px-2" style="
                            background-image: linear-gradient(135deg, #b00000 0, #b00000 100%);
                          ">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-doodr-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Loss</h6>
              <span class="data-1"> {{ getPriceFormat(loss) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #4390fc">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-ospen"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">City Ledger</h6>
              <span class="data-1"> {{ getPriceFormat(totalIncomes.City_ledger) || 0 }}</span>
            </div>
          </div>
        </div>
      </div>
    </v-row>

    <v-row>
      <v-col md="3">
        <!-- <div class="ml-4">Filter</div> -->

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
        ]" dense placeholder="Type" outlined :hide-details="true" item-text="name" item-value="id"
          @change="commonMethod"></v-select>
      </v-col>

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
      <v-col md="2" class="p-2 text-center" v-if="filterType == 5">
        <DateRangePicker :disabled="false" key="taxable" :DPStart_date="from_date" :DPEnd_date="to_date"
          column="date_range" @selected-dates="handleDatesFilter" />
      </v-col>
    </v-row>

    <v-row>
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-tabs v-model="activeTab" :vertical="vertical" background-color="primary" dark show-arrows>
          <v-spacer></v-spacer>
          <v-tab active-class="active-link"> Income List </v-tab>
          <v-tab active-class="active-link"> {{ Model }} List </v-tab>
          <v-tab active-class="active-link" v-if="can('management_income_view')">
            Management {{ Model }} List
          </v-tab>

          <v-tabs-slider color="#1259a7"></v-tabs-slider>
          <!-- today checkin -->

          <v-tab-item>
            <v-col md="12">
              <v-card class="mb-5 rounded-md mt-3" elevation="0">
                <v-toolbar class="rounded-md" color="background" dense flat dark>
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
                      <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
                        @click="process('income_report_download')">
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
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>

                  <tr v-for="(item, index) in incomeData" :key="index">
                    <td>{{ ++index }}</td>
                    <td>{{ item.date }}</td>
                    <td>{{ item.time }}</td>
                    <td>
                      <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer">
                        {{ item.booking.reservation_no || "---" }}
                      </span>
                    </td>
                    <td style="max-width: 20px !important">{{ item.room }}</td>
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
                      <span v-if="(item &&
                        item.payment_mode &&
                        item.payment_mode.name) == 'Cash' && i == 1
                        ">
                        {{ item.amount }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Bank' && i == 4
                        ">
                        {{ item.amount }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Online' && i == 3
                        ">
                        {{ item.amount }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'UPI' && i == 5
                        ">
                        {{ item.amount }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Card' && i == 2
                        ">
                        {{ item.amount }}
                      </span>

                      <span v-else-if="(item && item.payment_mode.name) == 'City Ledger' &&
                        i == 7
                        ">
                        {{ item.amount }}
                      </span>

                      <span v-else> --- </span>
                    </td>
                  </tr>
                  <tr class="text-right">
                    <th colspan="7">Total</th>
                    <th>{{ totalIncomes.Cash }}</th>
                    <th>{{ totalIncomes.Card }}</th>
                    <th>{{ totalIncomes.Online }}</th>
                    <th>{{ totalIncomes.Bank }}</th>
                    <th>{{ totalIncomes.UPI }}</th>
                    <th>{{ totalIncomes.Cheque }}</th>
                    <th>{{ totalIncomes.City_ledger }}</th>
                  </tr>
                </table>
              </v-card>
            </v-col>
          </v-tab-item>

          <v-tab-item>
            <v-col md="12">
              <v-card class="mb-5 rounded-md mt-3" elevation="0">
                <v-toolbar class="rounded-md" color="background" dense flat>
                  <v-spacer></v-spacer>
                  <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                        @click="process('expense_report_print')">
                        <v-icon class="white--text">mdi-printer-outline</v-icon>
                      </v-btn>
                    </template>
                    <span>PRINT</span>
                  </v-tooltip>

                  <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
                        @click="process('expense_report_download')">
                        <v-icon class="white--text">mdi-download-outline</v-icon>
                      </v-btn>
                    </template>
                    <span> DOWNLOAD </span>
                  </v-tooltip>
                </v-toolbar>

                <table>
                  <tr>
                    <th v-for="(item, index) in ExpenseHeaders" :key="index">
                      <span v-html="item.text"></span>
                    </th>
                  </tr>
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>
                  <tr v-for="(item, index) in expenseData" :key="index">
                    <td>{{ ++index }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>{{ item.time }}</td>
                    <td>{{ item.item }}</td>
                    <td>{{ item.qty }}</td>

                    <td v-for="i in 6" :key="i" class="text-right">
                      <span v-if="(item && item.payment_mode.name) == 'Cash' && i == 1
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Bank' && i == 4
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Online' && i == 3
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'UPI' && i == 5
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Card' && i == 2
                        ">
                        {{ item.total }}
                      </span>

                      <span v-else> --- </span>
                    </td>
                  </tr>
                  <tr class="text-right">
                    <th colspan="5">Total</th>
                    <th>{{ totalExpenses.Cash }}</th>
                    <th>{{ totalExpenses.Card }}</th>
                    <th>{{ totalExpenses.Online }}</th>
                    <th>{{ totalExpenses.Bank }}</th>
                    <th>{{ totalExpenses.UPI }}</th>
                    <th>{{ totalExpenses.Cheque }}</th>
                  </tr>
                </table>
              </v-card>
            </v-col>
          </v-tab-item>

          <v-tab-item>
            <v-col md="12" v-if="can('management_income_view')">
              <v-card class="mb-5 rounded-md mt-3" elevation="0">
                <v-toolbar class="rounded-md" color="background" dense flat>
                  <v-spacer></v-spacer>
                  <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                        @click="process('expense_report_print')">
                        <v-icon class="white--text">mdi-printer-outline</v-icon>
                      </v-btn>
                    </template>
                    <span>PRINT</span>
                  </v-tooltip>

                  <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
                        @click="process('expense_report_download')">
                        <v-icon class="white--text">mdi-download-outline</v-icon>
                      </v-btn>
                    </template>
                    <span> DOWNLOAD </span>
                  </v-tooltip>
                </v-toolbar>

                <table>
                  <tr>
                    <th v-for="(item, index) in ExpenseHeaders" :key="index">
                      <span v-html="item.text"></span>
                    </th>
                  </tr>
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>
                  <tr v-for="(item, index) in managementExpenseData" :key="index">
                    <td>{{ ++index }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>{{ item.time }}</td>
                    <td>{{ item.item }}</td>
                    <td>{{ item.qty }}</td>

                    <td v-for="i in 6" :key="i" class="text-right">
                      <span v-if="(item && item.payment_mode.name) == 'Cash' && i == 1
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Bank' && i == 4
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Online' && i == 3
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'UPI' && i == 5
                        ">
                        {{ item.total }}
                      </span>
                      <span v-else-if="(item && item.payment_mode.name) == 'Card' && i == 2
                        ">
                        {{ item.total }}
                      </span>

                      <span v-else> --- </span>
                    </td>
                  </tr>
                  <tr class="text-right">
                    <th colspan="5">Total</th>
                    <th>{{ managementExpense.Cash }}</th>
                    <th>{{ managementExpense.Card }}</th>
                    <th>{{ managementExpense.Online }}</th>
                    <th>{{ managementExpense.Bank }}</th>
                    <th>{{ managementExpense.UPI }}</th>
                    <th>{{ managementExpense.Cheque }}</th>
                  </tr>
                </table>
              </v-card>
            </v-col>
          </v-tab-item>
        </v-tabs>
      </v-card>
    </v-row>
  </div>
  <NoAccess v-else />
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
    endpoint: "expense",
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
    ExpenseHeaders: [
      { text: "#" },
      { text: "Date" },
      { text: "Time" },
      { text: "Item" },
      { text: "QTY" },
      { text: "Cash" },
      { text: "Card" },
      { text: "Online" },
      { text: "Bank" },
      { text: "UPI" },
      { text: "Cheque" },
    ],
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
    this.getExpenseData();
    this.getManagementExpenseData();
    this.getIncomeData();
    this.get_counts();
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
      const startOfWeek = new Date(
        today.getFullYear(),
        today.getMonth(),
        today.getDate() - dayOfWeek
      );
      const endOfWeek = new Date(
        today.getFullYear(),
        today.getMonth(),
        startOfWeek.getDate() + 6
      );

      return [
        startOfWeek.toISOString().slice(0, 10),
        endOfWeek.toISOString().slice(0, 10),
      ];
    },
  },

  methods: {
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    handleDatesFilter(dates) {

      this.from_date = dates[0];
      this.to_date = dates[1];
      if (this.from_date && this.to_date) {
        this.getExpenseData();
        this.getManagementExpenseData();
        this.getIncomeData();
        this.get_counts();
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
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
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

    getFirstAndLastDay() {
      const currentDate = new Date();
      const day = currentDate.getDate();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
      const year = currentDate.getFullYear();
      const last = new Date(year, month, 0).getDate().toString().padStart(2, "0");

      let firstDay = `${year}-${month}-0${1}`;
      let lastDay = `${year}-${month}-0${last}`;

      return [
        firstDay,
        lastDay
      ]
    },

    commonMethod() {
      const today = new Date();
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
        //    case 4:
        // this.from_date = new Date(today.getFullYear(), today.getMonth(), 1);
        // this.to_date = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        // break;

        // default:
        //   this.from_date = new Date().toJSON().slice(0, 10);
        //   this.to_date = new Date().toJSON().slice(0, 10);
        //   break;
      }

      this.getExpenseData();
      this.getIncomeData();
      this.get_counts();
      this.getManagementExpenseData();
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

    getExpenseData(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
          is_account: true,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.expenseData = data;
        // this.pagination.current = data.current_page;
        // this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    getManagementExpenseData(url = "management_expense") {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,

          from_date: this.from_date,
          to_date: this.to_date,
          is_account: true,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.managementExpenseData = data;
        console.log(data);
        // this.pagination.current = data.current_page;
        // this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    getIncomeData(url = "payments") {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          status: this.pagination.status,
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,

          from_date: this.from_date,
          to_date: this.to_date,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.incomeData = data;
      });
    },

    // commonMethod() {
    //   const today = new Date();

    //   switch (this.filterType) {
    //     case 1:
    //       this.from_date = new Date().toJSON().slice(0, 10);
    //       this.to_date = new Date().toJSON().slice(0, 10);
    //       break;
    //     case 2:
    //       this.from_date = new Date(Date.now() - 86400000)
    //         .toISOString()
    //         .slice(0, 10);
    //       this.to_date = new Date(Date.now() - 86400000)
    //         .toISOString()
    //         .slice(0, 10);
    //       break;
    //     case 3:
    //       this.from_date = this.week[0];
    //       this.to_date = this.week[1];
    //       break;
    //     case 4:
    //       this.from_date = new Date(today.getFullYear(), today.getMonth(), 1);
    //       this.to_date = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    //       break;

    //     default:
    //       this.from_date = new Date().toJSON().slice(0, 10);
    //       this.to_date = new Date().toJSON().slice(0, 10);
    //       break;
    //   }

    //   console.log(this.from_date + "from");
    //   console.log(this.to_date + "to");
    //   console.log(this.filterType + "type");

    //   this.getPaymentReportsByUser();
    // },

    get_counts() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,

          from_date: this.from_date,
          to_date: this.to_date,
        },
      };
      this.$axios.get(`account_count`, payload).then(({ data }) => {
        this.counts = data;
        this.loss = data.loss.toFixed(2);
        this.profit = data.profit;
        this.totalExpenses = {
          ...data.expense,
        };
        this.managementExpense = {
          ...data.managementExpense,
        };

        this.totalIncomes = {
          ...data.income,
        };
      });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 5px;
  font-size: 13px;
}

table,
th,
td {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
}

/* tr:nth-child(even) {
  background-color: #e9e9e9;
} */
</style>

<style scoped src="@/assets/dashtem.css"></style>
