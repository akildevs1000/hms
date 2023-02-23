<template>
  <div>
    <v-row>
      <div class="col-xl-3 col-lg-6 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Total Income</h4>
              <span class="data-1">
                RS. {{ totalIncomes.OverallTotal || 0 }}</span
              >
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Total Expense</h4>
              <span class="data-1"
                >RS. {{ totalExpenses.OverallTotal || 0 }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 text-uppercase">
        <div class="card px-2 checkedIn">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Total Profit</h4>
              <span class="data-1"> RS. {{ profit }}</span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Total Loss</h4>
              <span class="data-1"> RS.{{ loss }}</span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </v-row>

    <v-row>
      <v-col md="3">
        <div class="ml-4">From</div>
        <v-col cols="12" sm="12" md="12">
          <v-menu
            v-model="from_menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="from_date"
                readonly
                v-bind="attrs"
                v-on="on"
                outlined
                dense
                :hide-details="true"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="from_date"
              @input="from_menu = false"
              @change="commonMethod"
            ></v-date-picker>
          </v-menu>
        </v-col>
      </v-col>
      <v-col md="3">
        <div class="ml-4">To</div>
        <v-col cols="12" sm="12" md="12">
          <v-menu
            v-model="to_menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="to_date"
                readonly
                v-bind="attrs"
                v-on="on"
                outlined
                dense
                :hide-details="true"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="to_date"
              @input="to_menu = false"
              @change="commonMethod"
            ></v-date-picker>
          </v-menu>
        </v-col>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text"> Income List</label>
          </v-toolbar>
          <table>
            <tr>
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
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
              <td>{{ item.created_at }}</td>
              <td>{{ item.time }}</td>
              <td>{{ item.booking.reservation_no }}</td>
              <td>{{ item.type }}</td>
              <td>{{ item.room }}</td>
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
                  v-else-if="
                    (item && item.payment_mode.name) == 'Bank' && i == 4
                  "
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
                  v-else-if="
                    (item && item.payment_mode.name) == 'UPI' && i == 5
                  "
                >
                  {{ item.amount }}
                </span>
                <span
                  v-else-if="
                    (item && item.payment_mode.name) == 'Card' && i == 2
                  "
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
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat>
            <label class="white--text"> {{ Model }} List</label>
          </v-toolbar>
          <table>
            <tr>
              <th v-for="(item, index) in ExpenseHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <v-progress-linear
              v-if="loading"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
            <tr v-for="(item, index) in expenseData" :key="index">
              <td>{{ ++index }}</td>
              <td>{{ item.created_at }}</td>
              <td>{{ item.time }}</td>
              <td>{{ item.item }}</td>
              <td>{{ item.qty }}</td>

              <td v-for="i in 6" :key="i" class="text-right">
                <span
                  v-if="(item && item.payment_mode.name) == 'Cash' && i == 1"
                >
                  {{ item.amount }}
                </span>
                <span
                  v-else-if="
                    (item && item.payment_mode.name) == 'Bank' && i == 4
                  "
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
                  v-else-if="
                    (item && item.payment_mode.name) == 'UPI' && i == 5
                  "
                >
                  {{ item.amount }}
                </span>
                <span
                  v-else-if="
                    (item && item.payment_mode.name) == 'Card' && i == 2
                  "
                >
                  {{ item.amount }}
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
    </v-row>
  </div>
</template>

<script>
export default {
  data: () => ({
    Model: "Expense",

    from_date: new Date().toJSON().slice(0, 10),
    from_menu: false,

    to_date: new Date().toJSON().slice(0, 10),
    to_menu: false,

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
      { text: "Reservation Number" },
      { text: "Type" },
      { text: "Rooms" },
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
    totalIncomes: {},
  }),

  created() {
    this.loading = true;
    this.getExpenseData();
    this.getIncomeData();
    this.get_counts();
  },

  methods: {
    onPageChange() {
      this.getExpenseData();
    },
    can(permission) {
      let user = this.$auth;
      return;
      return (
        (user && user.permissions.some((e) => e.permission == permission)) ||
        user.master
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

    onPageChange() {
      this.getExpenseData();
    },

    commonMethod() {
      this.getExpenseData();
      this.getIncomeData();
      this.get_counts();
    },

    getExpenseData(url = this.endpoint) {
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
        this.expenseData = data;
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
        this.loss = data.loss;
        this.profit = data.profit;
        this.totalExpenses = {
          ...data.expense,
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
