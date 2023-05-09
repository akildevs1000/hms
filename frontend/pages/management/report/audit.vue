<template>
  <div>
    <!-- <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Management / {{ Model }}</div>
      </v-col>
    </v-row> -->
    <v-row>
      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #800000">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Cash</h6>
              <span class="data-1">₹{{ GrandTotalCash || 0 }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #ffbe00">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Card</h6>
              <span class="data-1">₹{{ GrandTotalCard || 0 }} <br /> </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #74166d">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Bank</h6>
              <span class="data-1">₹{{ GrandTotalBank || 0 }} </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #00b300">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Online</h6>
              <span class="data-1">₹{{ GrandTotalTodayOnline || 0 }} </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #18069e">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">UPI</h6>
              <span class="data-1">₹{{ GrandTotalTodayUPI || 0 }} </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-4 text-uppercase">
        <div class="card px-2" style="background-color: #4390fc">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">City Ledger</h6>
              <span class="data-1">₹{{ GrandTotalBalance || 0 }} </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-6 text-uppercase">
        <div class="card px-2" style="background-color: #02ada4">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Expenses</h6>
              <span class="data-1">₹{{ totExpense || 0 }} </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 my-0 py-0 col-lg-3 col-md-6 text-uppercase">
        <div class="card px-2" style="background-color: #ce008e">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Income</h6>
              <span class="data-1">₹{{ GrandTotal || 0 }} </span>
            </div>
          </div>
        </div>
      </div>
    </v-row>
    <v-row>
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
              outlined
              label="Date"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="from_date"
            @input="from_menu = false"
            @change="commonMethod"
          ></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">Today Checkin Report</label>
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
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in todayCheckIn"
              :key="index"
              style="background-color: yellow"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.reservation_no }}
                </span>
              </td>
              <td class="room-width">
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>
              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">
                {{ setAdvancePayment(item.advance_price) }}
              </td>
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
              <th class="text-right" colspan="9">Total</th>
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

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">Continue Report</label>
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
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in continueRooms"
              :key="index"
              style="background-color: #9bc1e6"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.reservation_no }}
                </span>
              </td>
              <td class="room-width">
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
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
              <th class="text-right" colspan="9">Total</th>
              <th class="text-right">{{ continueTotalCash }}</th>
              <th class="text-right">{{ continueTotalCard }}</th>
              <th class="text-right">{{ continueTotalOnline }}</th>
              <th class="text-right">{{ continueTotalBank }}</th>
              <th class="text-right">{{ continueTotalUPI }}</th>
              <th class="text-right">{{ continueTotalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">CheckOut Report</label>
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
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in todayCheckOut"
              :key="index"
              style="background-color: #90d24d"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.reservation_no }}
                </span>
              </td>
              <td class="room-width">
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>

              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">
                {{ setAdvancePayment(item.advance_price) }}
              </td>
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
              <th class="text-right" colspan="9">Total</th>
              <th class="text-right">{{ checkoutTotalCash }}</th>
              <th class="text-right">{{ checkoutTotalCard }}</th>
              <th class="text-right">{{ checkoutTotalOnline }}</th>
              <th class="text-right">{{ checkoutTotalBank }}</th>
              <th class="text-right">{{ checkoutTotalUPI }}</th>
              <th class="text-right">{{ checkoutTotalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">Today Booking Report</label>
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
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in todayPayments"
              :key="index"
              style="background-color: #29b9ca"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.reservation_no }}
                </span>
              </td>
              <td class="room-width">
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>

              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">
                {{ setAdvancePayment(item.advance_price) }}
              </td>
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
              <th class="text-right" colspan="9">Total</th>
              <th class="text-right">{{ todayPaymentTotalCash }}</th>
              <th class="text-right">{{ todayPaymentTotalCard }}</th>
              <th class="text-right">{{ todayPaymentTotalOnline }}</th>
              <th class="text-right">{{ todayPaymentTotalBank }}</th>
              <th class="text-right">{{ todayPaymentTotalUPI }}</th>
              <th class="text-right">{{ todayPaymentTotalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">City Ledger Report</label>
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
              <th v-for="(item, index) in incomeHeaders" :key="index">
                <span v-html="item.text"></span>
              </th>
            </tr>
            <tr
              v-for="(item, index) in cityLedgerPaymentsAudit"
              :key="index"
              style="background-color: #e4dc94"
            >
              <td>{{ ++index }}</td>
              <td>{{ item && item.customer && item.customer.first_name }}</td>
              <td>
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.reservation_no }}
                </span>
              </td>
              <td class="room-width">
                <span
                  class="blue--text"
                  @click="goToRevView(item)"
                  style="cursor: pointer"
                >
                  {{ item.rooms }}
                </span>
              </td>

              <td>{{ item && item.source }}</td>
              <td>{{ item && item.check_in }}</td>
              <td>{{ item && item.check_out }}</td>
              <td class="text-right">{{ item.total_price }}</td>
              <td class="text-right">
                {{ setAdvancePayment(item.advance_price) }}
              </td>
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
              <th class="text-right" colspan="9">Total</th>
              <th class="text-right">{{ cityLedgerTotalCash }}</th>
              <th class="text-right">{{ cityLedgerTotalCard }}</th>
              <th class="text-right">{{ cityLedgerTotalOnline }}</th>
              <th class="text-right">{{ cityLedgerTotalBank }}</th>
              <th class="text-right">{{ cityLedgerTotalUPI }}</th>
              <th class="text-right">{{ cityLedgerTotalBalance }}</th>
            </tr>
          </table>
        </v-card>
      </v-col>

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <label class="white--text">Cancel Rooms</label>
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
              <th>Room No</th>
              <th>Room Type</th>
              <th>Time</th>
              <th>Amount</th>
              <th>Reason</th>
              <th>Cancel By</th>
            </tr>
            <tr v-for="(item, index) in cancelRooms" :key="index">
              <td>{{ item && item.room_no }}</td>
              <td>{{ item && item.room_type }}</td>
              <td>{{ item && item.time }}</td>
              <td class="text-right">{{ item && item.grand_total }}</td>
              <td>{{ item && item.reason }}</td>
              <td>{{ item && item.user && item.user.name }}</td>
            </tr>
          </table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import CheckinAudit from "../../../components/audit/CheckinAudit.vue";
export default {
  components: {
    CheckinAudit,
  },
  data: () => ({
    Model: "Audit Report",
    // from_date: new Date().toJSON().slice(0, 10),
    from_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      .toISOString()
      .substr(0, 10),
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
    cityLedgerPaymentsAudit: [],
    cancelRooms: [],
    counts: [],
    loading: false,
    total: 0,
    totExpense: 0,

    incomeHeaders: [
      { text: "#" },
      { text: "Guest" },
      { text: "Rev. No" },
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
    this.getdata();
  },

  computed: {
    totalBalance() {
      let sum = 0;
      this.todayCheckIn.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    totalCash() {
      return this.getSum(this.todayCheckIn, 1);
    },
    totalCard() {
      return this.getSum(this.todayCheckIn, 2);
    },
    totalBank() {
      return this.getSum(this.todayCheckIn, 4);
    },
    totalOnline() {
      return this.getSum(this.todayCheckIn, 3);
    },
    totalUPI() {
      return this.getSum(this.todayCheckIn, 5);
    },

    continueTotalBalance() {
      let sum = 0;
      this.continueRooms.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    continueTotalCash() {
      return this.getSum(this.continueRooms, 1);
    },
    continueTotalCard() {
      return this.getSum(this.continueRooms, 2);
    },
    continueTotalBank() {
      return this.getSum(this.continueRooms, 4);
    },
    continueTotalOnline() {
      return this.getSum(this.continueRooms, 3);
    },
    continueTotalUPI() {
      return this.getSum(this.continueRooms, 5);
    },

    checkoutTotalBalance() {
      let sum = 0;
      this.todayCheckOut.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    checkoutTotalCash() {
      return this.getSum(this.todayCheckOut, 1);
    },
    checkoutTotalCard() {
      return this.getSum(this.todayCheckOut, 2);
    },
    checkoutTotalBank() {
      return this.getSum(this.todayCheckOut, 4);
    },
    checkoutTotalOnline() {
      return this.getSum(this.todayCheckOut, 3);
    },
    checkoutTotalUPI() {
      return this.getSum(this.todayCheckOut, 5);
    },

    todayPaymentTotalBalance() {
      let sum = 0;
      this.todayPayments.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    todayPaymentTotalCash() {
      return this.getSum(this.todayPayments, 1);
    },
    todayPaymentTotalCard() {
      return this.getSum(this.todayPayments, 2);
    },
    todayPaymentTotalBank() {
      return this.getSum(this.todayPayments, 4);
    },
    todayPaymentTotalOnline() {
      return this.getSum(this.todayPayments, 3);
    },
    todayPaymentTotalUPI() {
      return this.getSum(this.todayPayments, 5);
    },

    cityLedgerTotalBalance() {
      let sum = 0;
      this.cityLedgerPaymentsAudit.map((e) => (sum += parseFloat(e.balance)));
      return sum.toFixed(2);
    },
    cityLedgerTotalCash() {
      return this.getSum(this.cityLedgerPaymentsAudit, 1);
    },
    cityLedgerTotalCard() {
      return this.getSum(this.cityLedgerPaymentsAudit, 2);
    },
    cityLedgerTotalBank() {
      return this.getSum(this.cityLedgerPaymentsAudit, 4);
    },
    cityLedgerTotalOnline() {
      return this.getSum(this.cityLedgerPaymentsAudit, 3);
    },
    cityLedgerTotalUPI() {
      return this.getSum(this.cityLedgerPaymentsAudit, 5);
    },

    GrandTotal() {
      let tot =
        parseFloat(this.GrandTotalCash) +
        parseFloat(this.GrandTotalCard) +
        parseFloat(this.GrandTotalBank) +
        parseFloat(this.GrandTotalTodayOnline) +
        parseFloat(this.GrandTotalTodayUPI);
      return tot.toFixed(2);
    },

    GrandTotalCash() {
      let tot =
        parseFloat(this.cityLedgerTotalCash) +
        parseFloat(this.totalCash) +
        // parseFloat(this.totalUPI) +
        parseFloat(this.checkoutTotalCash) +
        parseFloat(this.continueTotalCash) +
        parseFloat(this.todayPaymentTotalCash);
      return tot.toFixed(2);
    },

    GrandTotalCard() {
      let tot =
        parseFloat(this.totalCard) +
        parseFloat(this.checkoutTotalCard) +
        parseFloat(this.continueTotalCard) +
        parseFloat(this.todayPaymentTotalCard);
      parseFloat(this.cityLedgerTotalCard);
      return tot.toFixed(2);
    },

    GrandTotalBank() {
      let tot =
        parseFloat(this.continueTotalBank) +
        parseFloat(this.todayPaymentTotalBank) +
        parseFloat(this.checkoutTotalBank) +
        parseFloat(this.cityLedgerTotalBank) +
        parseFloat(this.totalBank);
      return tot.toFixed(2);
    },

    GrandTotalTodayOnline() {
      let tot =
        parseFloat(this.continueTotalOnline) +
        parseFloat(this.todayPaymentTotalOnline) +
        parseFloat(this.checkoutTotalOnline) +
        parseFloat(this.cityLedgerTotalOnline) +
        parseFloat(this.totalOnline);
      return tot.toFixed(2);
    },

    GrandTotalTodayUPI() {
      let tot =
        parseFloat(this.continueTotalUPI) +
        parseFloat(this.todayPaymentTotalUPI) +
        parseFloat(this.checkoutTotalUPI) +
        parseFloat(this.cityLedgerTotalUPI) +
        parseFloat(this.totalUPI);
      return tot.toFixed(2);
    },

    GrandTotalBalance() {
      let tot =
        parseFloat(this.totalBalance) +
        parseFloat(this.continueTotalBalance) +
        parseFloat(this.checkoutTotalBalance) +
        parseFloat(this.cityLedgerTotalBalance) +
        parseFloat(this.todayPaymentTotalBalance);
      return tot.toFixed(2);
    },
  },

  methods: {
    onPageChange() {
      this.getExpenseData();
    },
    can(permission) {
      return this.$auth.user.user_type == "company" ? true : false;
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

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    getSum(item, type) {
      let sum = 0;
      item.map((e) => {
        e.transactions.map((e) =>
          e.payment_method_id == type ? (sum += parseFloat(e.credit)) : 0
        );
      });
      return sum.toFixed(2);
    },

    setAdvancePayment(amt) {
      return amt > 0 ? amt : 0;
    },

    process(type) {
      alert("coming soon, developing");
      return;
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },

    getPaymentMode(item, mode) {
      // let creditTrans = item.transactions.filter((e) => e.credit > 0);
      let creditTrans = item.transactions;
      switch (mode) {
        case 1:
          return this.getPaySum(creditTrans, 1);
        case 2:
          return this.getPaySum(creditTrans, 2);
        case 3:
          return this.getPaySum(creditTrans, 3);
        case 4:
          return this.getPaySum(creditTrans, 4);
        case 5:
          return this.getPaySum(creditTrans, 5);
        default:
          break;
      }
    },

    getPaySum(payload, mode) {
      let sum = 0;
      payload.map((e) => {
        if (e.payment_method_id == mode) {
          sum += parseFloat(e.credit);
        }
      });
      return sum.toFixed(2);
    },

    commonMethod() {
      this.getdata();
    },

    getdata(url = "get_audit_report") {
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          // date: "2023-04-10",
          date: this.from_date,
        },
      };
      this.$axios.get(url, options).then(({ data }) => {
        this.todayCheckIn = data.todayCheckIn;
        this.continueRooms = data.continueRooms;
        this.todayCheckOut = data.todayCheckOut;
        this.todayPayments = data.todayPayments;
        this.cityLedgerPaymentsAudit = data.cityLedgerPaymentsAudit;
        this.cancelRooms = data.cancelRooms;
        this.totExpense = data.totExpense;
      });
    },
  },
};
</script>

<style scoped src="@/assets/dashtem.css"></style>
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

.dashboard-payment-card {
  margin: 10px;
  background-color: #fff;
  border-radius: 5px;
  padding: 5px 20px;
}

.room-width {
  max-width: 20px !important;
}
</style>
