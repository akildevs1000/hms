<template>
  <div v-if="can(`agents_access`)">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top
        absolute
        color="secondary"
        elevation="24"
      >
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="agentPaymentDialog" persistent max-width="700px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>City Ledger Payment</span>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <table>
              <v-progress-linear
                v-if="loading"
                :active="loading"
                :indeterminate="loading"
                absolute
                color="primary"
              ></v-progress-linear>
              <tr>
                <th>Customer Name</th>
                <td style="width: 300px">
                  {{ booking && booking.title }}
                </td>
              </tr>
              <!-- <tr>
                <th>Room No</th>
                <td>
                  {{ booking.room_no }}
                </td>
              </tr>
              <tr>
                <th>Room Type</th>
                <td>
                  {{ booking.room_type }}
                </td>
              </tr> -->
              <tr>
                <th>Check In</th>
                <td>
                  {{ booking && booking.check_in_date }}
                </td>
              </tr>
              <tr>
                <th>Check Out</th>
                <td>
                  {{ booking && booking.check_out_date }}
                </td>
              </tr>
              <tr>
                <th>
                  Payment Mode
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-select
                    v-model="booking.payment_mode_id"
                    :items="[
                      { id: 1, name: 'Cash' },
                      { id: 2, name: 'Card' },
                      { id: 3, name: 'Online' },
                      { id: 4, name: 'Bank' },
                      { id: 5, name: 'UPI' },
                      { id: 6, name: 'Cheque' }
                    ]"
                    item-text="name"
                    item-value="id"
                    dense
                    outlined
                    :hide-details="true"
                    :height="1"
                  ></v-select>
                </td>
              </tr>
              <tr>
                <th>Total Amount</th>
                <td>{{ booking && booking.total_price }}</td>
              </tr>
              <tr>
                <th>Total Posting Amount</th>
                <td>{{ booking && booking.total_posting_amount }}</td>
              </tr>
              <tr>
                <th>previous Credits</th>
                <td>{{ totalCredit || 0 }}</td>
              </tr>
              <tr>
                <th>Remaining Balance</th>
                <td>{{ booking.grand_remaining_price }}</td>
              </tr>
              <tr>
                <th>
                  Full Payment
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="booking.full_payment"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr></tr>
            </table>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn class="primary" small @click="store_customer_payment"
            >Save</v-btn
          >
          <v-btn class="error" small @click="agentPaymentDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>
    <v-row>
      <!-- <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="custom-text-box shadow-none"
          @change="getDataFromApi(`booking_agents`)"
          v-model="pagination.per_page"
          :items="[50, 100, 500, 1000]"
          placeholder="Per Page Records"
          solo
          dense
          flat
          :hide-details="true"
        ></v-select>
      </v-col> -->
      <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="custom-text-box shadow-none"
          v-model="type"
          :items="types"
          dense
          placeholder="Type"
          solo
          flat
          :hide-details="true"
        ></v-select>
      </v-col>

      <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="custom-text-box shadow-none"
          v-model="source"
          :items="type == 'Online' ? sources : agentList"
          dense
          placeholder="Sources"
          solo
          flat
          :hide-details="true"
          @change="getDataFromApi('agents')"
        ></v-select>
      </v-col>
      <v-col md="3">
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
              dense
              :hide-details="true"
              class="custom-text-box shadow-none"
              solo
              flat
              label="From"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="from_date"
            @input="from_menu = false"
            @change="commonMethod"
          ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
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
              dense
              class="custom-text-box shadow-none"
              solo
              flat
              label="To"
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
    </v-row>

    <div v-if="can(`agents_view`)">
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span> {{ Model }} List</span>
        </v-toolbar>
        <table>
          <tr style="font-size:15px">
            <th v-for="(item, index) in headers" :key="index">
              {{ item.text }}
            </th>
          </tr>
          <v-progress-linear
            v-if="loading"
            :active="loading"
            :indeterminate="loading"
            absolute
            color="primary"
          ></v-progress-linear>
          <tr v-for="(item, index) in data" :key="index" style="font-size:14px">
            <td>
              <b>{{ ++index }}</b>
            </td>
            <td>{{ item.booking_id || "---" }}</td>
            <td>{{ (item && item.customer.full_name) || "---" }}</td>
            <td>{{ item.type || "---" }}</td>
            <td>{{ (item && item.booking.rooms) || "---" }}</td>
            <td>{{ item.source || "---" }}</td>
            <td>{{ item.amount || "---" }}</td>
            <td>{{ item.posting_amount || 0 }}.00</td>
            <td>{{ (item && item.booking.check_in_date) || "---" }}</td>
            <td>{{ (item && item.booking.check_out_date) || "---" }}</td>
            <td>{{ item.booking_date || "---" }}</td>
            <td>
              <v-chip
                class="ma-2"
                :color="item.is_paid == 1 ? 'green' : 'red'"
                text-color="white"
              >
                {{ item.is_paid == 1 ? "Paid" : "Pending" }}
              </v-chip>
            </td>
            <td>{{ item.paid_date || "---" }}</td>
            <td>
              <!-- <v-icon
                x-small
                color="primary"
                @click="viewAgentsBilling(item)"
                class="mr-2"
              >
                mdi-eye
              </v-icon> -->
              <v-icon
                v-if="!item.is_paid"
                x-small
                color="primary"
                @click="paidAmount(item)"
                class="mr-2"
              >
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
              <v-pagination
                v-model="pagination.current"
                :length="pagination.total"
                @input="onPageChange"
                :total-visible="12"
              ></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    agentPaymentDialog: false,
    snackbar: false,
    response: "",

    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,

    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    Model: "City Ledger",
    endpoint: "city_ledger",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    type: "",
    source: "",
    agentList: ["Select All", "agent1", "agent2", "agent3", "agent4", "agent5"],
    types: ["Online", "Travel Agency"],
    sources: [
      "Select All",
      "MakeMyTrip",
      "OYO Rooms",
      "Airbnb.co.in",
      "Expedia.co.in",
      "Trivago.in",
      "Yatra",
      "Cleartrip",
      "in.hotels.com",
      "Booking.com",
      "TripAdvisor.in"
    ],

    headers: [
      {
        text: "#"
      },
      {
        text: "Booking Number"
      },
      {
        text: "Customer"
      },
      {
        text: "Type"
      },
      {
        text: "Rooms"
      },
      {
        text: "Source"
      },
      {
        text: "Amount"
      },
      {
        text: "Posting Amount"
      },
      {
        text: "Check In"
      },
      {
        text: "Check Out"
      },
      {
        text: "Booking Date"
      },
      {
        text: "Payment Status"
      },
      {
        text: "Paid Date"
      },
      {
        text: "Action"
      }
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    booking: [],
    agentData: [],
    totalCredit: 0,
    errors: []
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    }
  },
  created() {
    this.loading = true;
  },
  mounted() {
    this.getDataFromApi();
  },

  methods: {
    onPageChange() {
      this.getDataFromApi();
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    paidAmount(agentData) {
      this.agentData = agentData;
      let payload = {
        params: {
          id: agentData.booking_id,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`get_agent_booking`, payload).then(({ data }) => {
        if (data.status) {
          this.totalCredit = data.totalCredit;
          console.log(this.transaction);
          this.booking = data.data;
          this.booking.full_payment = "";
          this.bookingStatus = data.booking_status;
          this.customerId = data.customer_id;
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
          source: newSource
        }
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    store_customer_payment() {
      if (this.booking.full_payment == "") {
        alert("enter full payment");
        return true;
      }
      let payload = {
        agentData: this.agentData,
        booking_id: this.booking.id,
        remaining_price: this.booking.remaining_price,
        full_payment: this.booking.full_payment,
        payment_mode_id: this.booking.payment_mode_id
      };
      // return;
      this.$axios
        .post("/payment_by_customer", payload)
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
        .catch(e => console.log(e));
    },

    searchIt() {
      let s = this.search.length;
      let search = this.search;
      if (s == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (s > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${search}`);
      }
    }
  }
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
