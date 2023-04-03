<template>
  <div>
    <!-- <v-row>
      <div class="col-xl-3 col-lg-6 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Income</h6>
              <span class="data-1">
                RS. {{ convert_decimal(totalIncomes.OverallTotal) || 0 }}</span
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
      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Expense</h6>
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
              <i class="fas fa-doosr-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Profit</h6>
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
      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-doodr-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Loss</h6>
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
      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2" style="background-color: #4390fc">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-ospen"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">City Ledger</h6>
              <span class="data-1">
                RS.{{ totalIncomes.City_ledger || 0 }}</span
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
    </v-row> -->

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

      <v-col md="3" v-if="userList.length > 0">
        <div class="ml-4">Users</div>
        <v-col cols="12" sm="12" md="12">
          <v-select
            @change="getPaymentReportsByUser()"
            v-model="user_id"
            :items="userList"
            item-text="name"
            item-value="id"
            outlined
            dense
            placeholder="User List"
            :hide-details="true"
            flat
          ></v-select>
        </v-col>
      </v-col>
    </v-row>

    <v-row class="mt-0 mt-0">
      <v-col md="12">
        <v-card
          class="mb-5 rounded-md mt-3"
          elevation="0"
          v-for="(user, index) in paymentReportsByUser"
          :key="index"
        >
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">{{ user.name }}</label>
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
          </v-toolbar>
          <table>
            <tr>
              <th v-for="(item, index) in Header" :key="index">
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

            <tr v-for="(trans, index) in user.transactions" :key="index">
              <td>{{ ++index }}</td>
              <td>{{ trans.desc }}</td>
              <td>{{ trans.created_at }}</td>
              <td>{{ trans.time }}</td>
              <td>{{ trans.credit }}</td>
              <td>{{ trans.debit }}</td>
              <td v-for="i in 7" :key="i" class="text-right">
                <span
                  v-if="
                    (trans && trans.payment_mode && trans.payment_mode.name) ==
                      'Cash' && i == 1
                  "
                >
                  {{ trans.credit }}
                </span>
                <span
                  v-else-if="
                    (trans && trans.payment_mode && trans.payment_mode.name) ==
                      'Card' && i == 2
                  "
                >
                  {{ trans.credit }}
                </span>
                <span
                  v-else-if="
                    (trans && trans.payment_mode && trans.payment_mode.name) ==
                      'Online' && i == 3
                  "
                >
                  {{ trans.credit }}
                </span>
                <span
                  v-else-if="
                    (trans && trans.payment_mode && trans.payment_mode.name) ==
                      'Bank' && i == 4
                  "
                >
                  {{ trans.credit }}
                </span>

                <span
                  v-else-if="
                    (trans && trans.payment_mode && trans.payment_mode.name) ==
                      'UPI' && i == 5
                  "
                >
                  {{ trans.credit }}
                </span>

                <span
                  v-else-if="
                    (trans && trans.payment_mode && trans.payment_mode.name) ==
                      'City Ledger' && i == 7
                  "
                >
                  {{ trans.debit }}
                </span>

                <span v-else> --- </span>
              </td>
            </tr>
            <tr class="text-right">
              <th colspan="6">Total</th>
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

    Header: [
      { text: "#" },
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
    } else {
      this.user_id = this.$auth.user.id;
    }
    this.getPaymentReportsByUser();
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

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    onPageChange() {
      this.getExpenseData();
    },

    commonMethod() {
      this.getPaymentReportsByUser();
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
</style>

<style scoped src="@/assets/dashtem.css"></style>
