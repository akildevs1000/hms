<template>
  <div v-if="can('ledger_agents_access') && can('ledger_agents_view')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top absolute color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>


    <v-dialog v-model="payingDialog" persistent max-width="1000px">
      <v-card>
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
          <span>Payment</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="payingDialog = false">mdi-close</v-icon>
        </v-toolbar>
        <v-card-text>
          <Paying :msg="'payment by agent'" :BookingData="bookingData" @close-dialog="payingDialog = false"></Paying>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-row class="mt-5 mb-0">
      <v-col cols="6">
        <h3>{{ Model }}</h3>

      </v-col>
    </v-row>

    <v-row class="pt-0 mt-0">
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" @input="searchIt"
          v-model="search" hide-details></v-text-field>
      </v-col>
      <v-col xs="12" sm="12" md="1" cols="12">
        <v-select v-model="paid_status_type" :items="['Select All', 'Pending', 'Paid']" dense outlined
          placeholder="Payment" solo flat :hide-details="true" @change="getDataFromApi('agents')"></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="1" cols="12">
        <v-select v-model="type" :items="types" dense placeholder="Type" solo outlined flat :hide-details="true"
          @change="getDataFromApi('agents')"></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-select v-model="source" :items="type == 'Online' ? sources : agentList" outlined dense item-value="name"
          item-text="name" placeholder="Sources" solo flat :hide-details="true"
          @change="getDataFromApi('agents')"></v-select>
      </v-col>

      <v-col xs="12" sm="12" md="2" cols="12">
        <v-select v-model="guest_mode" :items="['Select All', 'Arrival', 'Departure']" outlined dense placeholder="Type"
          solo flat :hide-details="true" @change="getDataFromApi('agents')"></v-select>
      </v-col>
      <v-col xs="12" sm="12" md="4" cols="12">
        <!-- <DateRangePicker :disabled="false" key="taxable" :DPStart_date="from_date" :DPEnd_date="to_date"
          column="date_range" @selected-dates="handleDatesFilter" /> -->
        <CustomFilter @filter-attr="filterAttr" :defaultFilterType="4" />
      </v-col>

      <!-- <v-col md="2">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
              class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="2">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense class="custom-text-box shadow-none"
              solo flat label="To" :hide-details="true"></v-text-field>
          </template>
          <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col> -->
    </v-row>

    <div>
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
          <span> {{ Model }} List</span>
          <v-spacer></v-spacer>
          <!-- <v-btn class="primary" small @click="agentCreate">New</v-btn> -->
        </v-toolbar>
        <table>
          <tr style="font-size: 12px">
            <th v-for="(item, index) in headers" :key="index">
              {{ item.text }}
            </th>
          </tr>
          <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
            color="primary"></v-progress-linear>
          <tr v-for="(item, index) in data" :key="index" style="font-size: 12px">
            <td>
              <b>{{ ++index }}</b>
            </td>
            <td>
              <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer">
                {{ (item && item.reservation_no) || "---" }}
              </span>
            </td>
            <td style="max-width: 80px">{{ (item && item.rooms) || "---" }}</td>
            <td>{{ item.booking_date || "---" }}</td>
            <td>{{ item.reference_no || "---" }}</td>
            <td>
              {{ (item && item.customer && item.customer.first_name) || "---" }}
            </td>
            <td>{{ item.type || "---" }}</td>
            <td>{{ item.source || "---" }}</td>
            <td>{{ item.total_price || "0" }}</td>
            <td>{{ item.total_posting_amount || "0" }}</td>
            <td>
              {{
                parseFloat(item.total_price) +
                parseFloat(item.total_posting_amount) || "---"
              }}.00
            </td>
            <td>
              {{ (item && item.check_in_date) || "---" }}
            </td>
            <td>
              {{ (item && item.check_out_date) || "---" }}
            </td>
            <td>{{ item.paid_amounts || "---" }}</td>
            <td>{{ item.balance || "---" }}</td>
            <td>
              <v-chip x-small class="ma-2" :color="is_paid_color(item.balance)" text-color="white">
                {{ is_paid_text(item.balance) }}
              </v-chip>
            </td>
            <td>
              <v-icon v-if="can('ledger_agents_edit')" x-small color="primary" @click="paidAmount(item)" class="mr-2">
                mdi-cash-multiple
              </v-icon>
            </td>
          </tr>
        </table>
      </v-card>
      <div>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                :total-visible="12"></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>

  </div>
  <NoAccess v-else />
</template>
<script>
import Paying from "../../components/booking/Paying.vue";
import CustomFilter from "../../components/filter/CustomFilter.vue";
export default {
  components: {
    Paying,
    CustomFilter
  },
  data: () => ({
    radioGroup: 1,

    payingDialog: false,
    agentPaymentDialog: false,
    snackbar: false,
    response: "",
    paid_status_type: "",

    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Agents",
    endpoint: "agents",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    type: "",
    source: "",
    agentList: [],
    types: ["Online", "Travel Agency"],
    sources: [],
    guest_mode: "",
    headers: [
      {
        text: "#",
      },
      {
        text: "Rev.No",
      },
      {
        text: "Rooms",
      },
      {
        text: "Rev. Date",
      },
      {
        text: "Reference Number",
      },
      {
        text: "Customer",
      },
      {
        text: "Type",
      },

      {
        text: "Source",
      },
      {
        text: "Amount",
      },
      {
        text: "Posting",
      },
      {
        text: "Total",
      },
      {
        text: "C/In",
      },
      {
        text: "C/Out",
      },
      {
        text: "Paid Amount",
      },
      {
        text: "Balance",
      },
      {
        text: "Paid/Status",
      },
      {
        text: "Action",
      },
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    bookingData: {},
    booking: {},
    paid_status: 1,
    agentData: [],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },
  created() {
    this.month = new Date().getMonth();
    this.year = new Date().getFullYear();
    this.from_date = this.formatDate(new Date(this.year, this.month, 1));
    this.to_date = this.formatDate(new Date(this.year, this.month + 1, 0));

    this.loading = true;
    this.get_agents();
    this.get_online();
  },
  mounted() {
    //this.getDataFromApi();
  },

  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      //this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date)
        this.getDataFromApi();
    },

    // handleDatesFilter(dates) {

    //   this.from_date = dates[0];
    //   this.to_date = dates[1];
    //   if (this.from_date && this.to_date)
    //     this.getDataFromApi();
    // },
    getPriceFormat(price) {

      return parseFloat(price).toLocaleString('en-IN', {
        maximumFractionDigits: 2,

      });
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    onPageChange() {
      this.getDataFromApi();
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    is_paid_color(blc) {
      let s;
      if (blc > 0) {
        s = parseInt(0);
      } else {
        s = parseInt(1);
      }

      switch (s) {
        case 0:
          return "red";
          break;
        case 1:
          return "green";
          break;
      }
    },

    is_paid_text(blc) {
      let s;
      if (blc > 0) {
        s = parseInt(1);
      } else {
        s = parseInt(0);
      }
      switch (s) {
        case 0:
          return "paid";
          break;
        case 1:
          return "pending";
          break;
      }
    },

    getFullPayment() {
      let status = this.paid_status;
      switch (status) {
        case 1:
          this.booking.full_payment = this.booking.total_price;
          break;
        case 2:
          this.booking.full_payment = this.booking.total_posting_amount;
          break;
        default:
          this.booking.full_payment = this.booking.grand_remaining_price;
      }
    },

    paidAmount(item) {
      console.log(item);
      this.bookingData = item;
      this.payingDialog = true;
      return;

      this.agentData = agentData;
      let payload = {
        params: {
          id: agentData.booking_id,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_agent_booking`, payload).then(({ data }) => {
        if (data.status) {
          this.booking = data.data;
          this.booking.full_payment = "";
          this.bookingStatus = data.booking_status;
          this.customerId = data.customer_id;
          this.booking.full_payment = this.booking.total_price;
          this.agentPaymentDialog = true;
        }
      });
    },

    viewAgentsBilling(item) {
      this.$router.push(`/agents/details/${item.source}`);
    },

    commonMethod() {
      this.getDataFromApi();
    },

    agentCreate() {
      this.$router.push(`/Source/`);
    },

    get_agents() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_agent`, payload).then(({ data }) => {
        this.agentList = data;
      });
    },

    get_online() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_online`, payload).then(({ data }) => {
        this.sources = data;
      });
    },

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    getDataFromApi(url = this.endpoint) {
      let newSource = this.source;
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
          source: newSource,
          guest_mode: this.guest_mode,
          search: this.search,
          paid_status_type: this.paid_status_type,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    store_agent_payment() {
      if (
        this.booking.full_payment == ""
        // (this.booking.payment_mode_id == 4 && this.booking.transaction == "")
      ) {
        alert("fill required fields");
        return true;
      }
      let payload = {
        agentData: this.agentData,
        booking_id: this.booking.id,
        total_price: this.booking.remaining_price,
        posting_price: this.booking.total_posting_amount,
        total_with_posting: this.booking.grand_remaining_price,
        full_payment: this.booking.full_payment,
        payment_mode_id: this.booking.payment_mode_id,
        transaction: this.booking.transaction,
        paid_status: this.paid_status,
        user_id: this.$auth.user.id,
      };
      // return;
      console.log(payload);
      this.$axios
        .post("/payment_by_agent", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Payment successfully paid";
            this.agentPaymentDialog = false;
            this.getDataFromApi();
          }
        })
        .catch((e) => console.log(e));
    },

    searchIt() {
      if (this.search.length == 0) {
        this.getDataFromApi();
      } else if (this.search.length > 2) {
        this.getDataFromApi();
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
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}
</style>
