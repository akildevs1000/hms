<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="createReservationDialog" max-width="1200px">
      <Reservation :reservation="reservation" />
    </v-dialog>

    <v-dialog v-model="checkOutDialog" persistent max-width="1000px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="checkOutDialog = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-card-text>
          <check-out :BookingData="checkData" />
        </v-card-text>
        <v-card-actions>
          <!-- <v-btn class="primary" small @click="store_check_out">
              Check Out
            </v-btn> -->
          <!-- <v-btn class="error" small @click="checkOutDialog = false">
              Cancel
            </v-btn> -->
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="cancelDialog" persistent max-width="500">
      <v-card>
        <v-card-title class="text-h6">
          Are you sure you want to cancel this
        </v-card-title>
        <v-container grid-list-xs>
          <v-textarea
            placeholder="Reason"
            rows="3"
            dense
            outlined
            v-model="reason"
          ></v-textarea>
        </v-container>
        <v-card-actions>
          <v-btn class="primary" small :loading="loading" @click="cancelItem">
            Yes
          </v-btn>
          <v-btn class="error" small @click="cancelDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="postingDialog" persistent max-width="700px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}</span>
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
                <th>Bill No</th>
                <td style="width: 300px">
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.bill_no"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <th>Customer Name</th>
                <td style="width: 300px">
                  {{ checkData && checkData.title }}
                </td>
              </tr>
              <tr>
                <th>Room No</th>
                <td>
                  {{ checkData.room_no }}
                </td>
              </tr>
              <tr>
                <th>Room Type</th>
                <td>
                  {{ checkData.room_type }}
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Item
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="text"
                    v-model="posting.item"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  QTY
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.qty"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Amount
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.amount"
                    :hide-details="true"
                    @keyup="get_amount_with_tax(posting.tax_type)"
                  ></v-text-field>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Type
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-select
                    v-model="posting.tax_type"
                    :items="[
                      { id: -1, name: 'select..' },
                      { name: 'Food' },
                      { name: 'Others' },
                      { name: 'Mesentery' },
                      { name: 'Bed' },
                    ]"
                    item-text="name"
                    item-value="id"
                    dense
                    outlined
                    :hide-details="true"
                    :height="1"
                    @change="get_amount_with_tax(posting.tax_type)"
                  ></v-select>
                </td>
              </tr>

              <tr style="background-color: white">
                <th>
                  Amount With Tax
                  <span class="text-danger">*</span>
                </th>
                <td>
                  {{ posting.amount_with_tax }}
                </td>
              </tr>
              <tr></tr>
            </table>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn class="primary" small @click="store_posting" :loading="loading"
            >Post</v-btn
          >
          <v-btn class="error" small @click="closePosting"> Cancel </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="viewPostingDialog" persistent max-width="900px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}</span>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <table>
              <tr>
                <th v-for="(item, index) in headers" :key="index">
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
              <tr v-for="(item, index) in postings" :key="index">
                <td>{{ ++index }}</td>
                <td>{{ caps(item.bill_no) }}</td>
                <td>{{ caps(item && item.booked_room.room_no) }}</td>
                <td>{{ caps(item && item.booked_room.room_type) }}</td>
                <td>{{ caps(item && item.booked_room.title) }}</td>
                <td>{{ caps(item.item) }}</td>
                <td>{{ caps(item.qty) }}</td>
                <td>{{ caps(item.amount) }}</td>
                <td>{{ caps(item.posting_date) }}</td>
              </tr>
            </table>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn class="error" small @click="viewPostingDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="checkInDialog"
      persistent
      :width="1366"
      class="checkin-models"
    >
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="close">mdi mdi-close-box</v-icon>
        </v-toolbar>
        <v-card-text>
          <!-- <check-in :BookingData="checkData"></check-in> -->
          <check-in :BookingData="checkData" />
        </v-card-text>
        <v-container></v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="payingAdvance" persistent max-width="700px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}</span>
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
                  {{ checkData && checkData.title }}
                </td>
              </tr>
              <tr>
                <th>Room No</th>
                <td>
                  {{ checkData.room_no }}
                </td>
              </tr>
              <tr>
                <th>Room Type</th>
                <td>
                  {{ checkData.room_type }}
                </td>
              </tr>
              <tr>
                <th>Check In</th>
                <td>
                  {{ checkData && checkData.check_in_date }}
                </td>
              </tr>
              <tr>
                <th>Check Out</th>
                <td>
                  {{ checkData && checkData.check_out_date }}
                </td>
              </tr>
              <tr>
                <th>
                  Payment Mode
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-select
                    v-model="checkData.payment_mode_id"
                    :items="[
                      { id: 1, name: 'Cash' },
                      { id: 2, name: 'Card' },
                      { id: 3, name: 'Online' },
                      { id: 4, name: 'Bank' },
                      { id: 5, name: 'UPI' },
                      { id: 6, name: 'Cheque' },
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
                <td>{{ checkData && checkData.total_price }}</td>
              </tr>
              <!-- <tr style="background-color:white">
                <th>
                  Advance Price
                </th>
                <td>
                  {{ checkData.advance_price }}
                </td>
              </tr> -->
              <tr>
                <th>Remaining Balance</th>
                <td>{{ checkData.grand_remaining_price }}</td>
              </tr>

              <tr style="background-color: white">
                <th>
                  New Advance
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="new_advance"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr></tr>
            </table>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn
            class="primary"
            small
            @click="store_advance(checkData)"
            :loading="loading"
            >Pay</v-btn
          >
          <v-btn class="error" small @click="payingAdvance = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div>
      <v-tooltip
        bottom
        color="background"
        :position-x="tx"
        :position-y="ty"
        absolute
        offset-y
        v-model="showTooltip"
      >
        <table style="border: none !important">
          <tr class="bg-background">
            <th>Customer Name</th>
            <td style="width: 300px">
              {{ checkData && checkData.title }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Room No</th>
            <td>
              {{ checkData.room_no }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Room Type</th>
            <td>
              {{ checkData.room_type }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Check In</th>
            <td>
              {{ checkData && checkData.check_in }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Check Out</th>
            <td>
              {{ checkData && checkData.check_out }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Total Amount</th>
            <td>
              {{ checkData && checkData.total_price }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Remaining Balance</th>
            <td>
              {{ checkData.remaining_price }}
            </td>
          </tr>
          <tr></tr>
        </table>
      </v-tooltip>
      <v-row class="flex" justify="center"> </v-row>
      <v-menu
        v-model="showMenu"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
      >
        <v-list>
          <v-list-item-group v-model="selectedItem">
            <v-list-item
              v-if="bookingStatus == 1"
              link
              @click="checkInDialog = true"
            >
              <v-list-item-title>Check In</v-list-item-title>
            </v-list-item>

            <v-list-item
              v-else-if="bookingStatus == 2"
              link
              @click="get_check_out"
            >
              <v-list-item-title>Check Out</v-list-item-title>
            </v-list-item>

            <div v-else-if="bookingStatus == 3">
              <v-list-item link @click="setAvailable">
                <v-list-item-title>Make Available</v-list-item-title>
              </v-list-item>
              <v-list-item link @click="setMaintenance">
                <v-list-item-title>Make Maintenance</v-list-item-title>
              </v-list-item>
            </div>

            <div v-else>
              <v-list-item link @click="setAvailable">
                <v-list-item-title>Make Available</v-list-item-title>
              </v-list-item>
            </div>

            <div v-if="bookingStatus == 2">
              <v-list-item link @click="postingDialog = true">
                <v-list-item-title>Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewPostingDialog = true">
                <v-list-item-title>View Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewBillingDialog">
                <v-list-item-title>View Billing</v-list-item-title>
              </v-list-item>
            </div>
            <v-list-item
              link
              @click="payingAdvance = true"
              v-if="bookingStatus <= 2 && checkData.paid_by != 2"
            >
              <v-list-item-title>Pay Advance</v-list-item-title>
            </v-list-item>

            <v-list-item
              link
              @click="cancelDialog = true"
              v-if="bookingStatus == 1"
            >
              <v-list-item-title>Cancel Room </v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>

    <v-dialog v-model="LoadingDialog" hide-overlay persistent width="300">
      <v-card color="primary" dark>
        <v-card-text class="py-3">
          Loading...
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row>
      <v-col cols="12">
        <FullCalendar :options="calendarOptions" style="background: #fff" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";
import CheckIn from "../../components/booking/CheckIn.vue";
import CheckOut from "../../components/booking/CheckOut.vue";

export default {
  components: {
    FullCalendar,
    CheckIn,
    CheckOut,
  },
  data() {
    return {
      dateConfirmationDialog: false,
      LoadingDialog: false,
      loading: false,
      snackbar: false,
      response: "",
      isDirty: true,
      createReservationDialog: false,
      payingAdvance: false,
      checkInDialog: false,
      checkOutDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,
      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      showTooltip: false,
      tx: 0,
      ty: 0,
      x: 0,
      y: 0,
      calendarOptions: {
        plugins: [interactionPlugin, dayGridPlugin, resourceTimelinePlugin],
        // now: "2022-11-07",
        now: "",
        editable: true,
        aspectRatio: 1.8,
        scrollTime: "00:00",
        displayEventTime: false,
        selectable: true,
        initialView: "resourceTimelineMonth",

        eventResizableFromStart: true, // enables resizing from the start of the event
        slotEventOverlap: false, // allows events to overlap time slots
        eventResizable: true,
        navLinks: true,
        resourceAreaWidth: "12%",

        resourceAreaColumns: [
          {
            headerContent: "Room",
            field: "room_no",
            width: "3%",
          },
          {
            headerContent: "Room Type",
            field: "room_type",
            width: "5%",
          },
        ],
        resources: [
          // { id: "103", room_no: "103", room_type: "king", eventColor: "green" },
          // { id: "104", room_no: "104", eventColor: "orange" }
        ],
        events: [
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "104",
          //   start: "2023-02-20 01:00:00",
          //   end: "2023-02-20 23:00:00",
          //   title: "e",
          //   background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)",
          //   eventBorderColor: "red",
          // },
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "104",
          //   start: "2023-02-20 01:00:00",
          //   end: "2023-02-20 23:00:00",
          //   title: "e",
          // },
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "103",
          //   start: "2023-02-20 01:00:00",
          //   end: "2023-02-20 23:00:00",
          //   title: "e",
          // },
        ],

        eventDidMount: (arg) => {
          const eventId = arg.event.id;
          const bookingStatus = arg.event.extendedProps.status;
          if (arg.event.extendedProps.background) {
            arg.el.style.background = arg.event.extendedProps.background;
          }

          arg.el.addEventListener("contextmenu", (jsEvent) => {
            this.showTooltip = false;
            this.show(eventId, jsEvent);
            jsEvent.preventDefault();
          });

          // arg.el.addEventListener("mouseover", jsEvent => {
          //   this.get_event_by_mouse_hover(eventId, jsEvent);
          // });

          arg.el.addEventListener("dblclick", (jsEvent) => {
            this.evenIid = eventId;
            this.isDbCLick = true;
            this.get_data();
          });

          arg.el.addEventListener("mouseleave", (jsEvent) => {
            this.showTooltip = false;
          });
        },

        select: (date, jsEvent, view, resourceObj, vv) => {
          let obj = date.resource.extendedProps;
          if (date.startStr < this.currentDate) {
            this.alert(
              "Missing!",
              "Please Select Current Date or Future Date",
              "error"
            );
            return;
          }
          console.log(date);
          this.create_reservation(date, obj);
        },

        eventResize: (arg, delta) => {
          let obj = {
            eventId: arg.event.id,
            start: this.convert_date_format(arg.event.start),
            end: this.convert_date_format(arg.event.end),
            roomId: arg.event._def.resourceIds[0],
          };
          console.log(obj);
          // return;

          this.change_date_by_drag(obj);
        },

        eventDrop: (arg, delta) => {
          let obj = {
            eventId: arg.event.id,
            start: this.convert_date_format(arg.event.start),
            end: this.convert_end_date_format(arg.event.start, arg.event.end),
            company_id: this.$auth.user.company.id,
            roomId: arg.event._def.resourceIds[0],
          };

          console.log(obj);
          this.change_room_by_drag(obj);
        },
      },
      headers: [
        { text: "#" },
        { text: "Bill Number" },
        { text: "Room No" },
        { text: "Room Type" },
        { text: "Customer" },
        { text: "Item" },
        { text: "QTY" },
        { text: "Amount" },
        { text: "Date" },
      ],
      errors: [],
      data: [],
      postings: [],
      checkData: {},
      evenIid: "",
      bookingStatus: "",
      reason: "",
      customerId: "",
      bookingId: "",

      document: null,
      new_payment: 0,
      new_advance: 0,
      reservation: {},
      posting: {
        item: "",
        qty: "",
        amount: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1,
      },
      RoomList: [],
      reservation: {
        check_in: null,
        check_out: null,
        room_no: "",
        room_id: "",
        room_type: "",
        price: "",
        origin_price: "",
        room_id: "",
        isCalculate: false,
      },
      isDbCLick: false,
      totalTransactionAmount: 0,
      transactions: [],
    };
  },

  created() {
    this.currentDate;
  },

  mounted() {
    this.room_list();
    this.get_events();
    document.querySelector(".fc-license-message").style.display = "none";
  },

  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.new_payment = 0;
      this.get_data();
    },

    postingDialog() {
      this.formTitle = "Posting";
      this.get_data();
    },

    checkOutDialog() {
      this.formTitle = "Check Out";
      this.get_data();
    },

    viewPostingDialog() {
      this.formTitle = "View Post";
      this.get_posting();
    },

    payingAdvance() {
      this.formTitle = "Advance Payment";
      this.new_advance = 0;
      this.get_data();
    },
  },
  computed: {
    getBalance() {
      let full_pay = this.checkData.full_payment;
      let remainingPrice = this.checkData.remaining_price;
      let balance = full_pay - remainingPrice;
      if (balance >= 0) {
        return balance;
      }
    },

    currentDate() {
      return (this.calendarOptions.now = new Date().toJSON().slice(0, 10));
    },
  },
  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    show(id, jsEvent) {
      this.LoadingDialog = true;
      this.evenIid = id;
      this.get_data(jsEvent);
    },

    show_context_menu(jsEvent) {
      if (!jsEvent) {
        return;
      }
      this.LoadingDialog = false;
      this.x = jsEvent.clientX;
      this.y = jsEvent.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    get_events() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`events_list`, payload).then(({ data }) => {
        this.calendarOptions.events = data;
        console.log(this.calendarOptions.events);
        return;
        let a2 = {
          id: "1",
          room_id: "1",
          resourceId: "104",
          start: "2023-02-20 01:00:00",
          end: "2023-02-20 23:00:00",
          title: "e",
          background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)",
          eventBorderColor: "red",
        };

        let a1 = {
          id: 390,
          room_id: 37,
          booking_id: 329,
          customer_id: "190",
          start: "2023-02-20 12:08:58",
          check_out: "2023-02-22 11:00:00",
          resourceId: "102",
          title: "fahath mohamed",
          background: "linear-gradient(135deg, #FFBE00 0, #FFBE00 100%)",
          check_out_time: "11:00",
          // end: "2023-02-23 11:00",
          end: "2023-02-23 11:00",
        };
        this.calendarOptions.events.push(a1),
          console.log(this.calendarOptions.events);
      });
    },

    // get_amount_with_tax(clause) {
    //   let per = clause == "Food" ? 5 : 12;
    //   let res = this.getPercentage(this.posting.amount, per);
    //   let gst = parseInt(res) / 2;
    //   this.posting.sgst = gst;
    //   this.posting.cgst = gst;
    //   this.posting.tax = res;
    //   this.posting.amount_with_tax =
    //     parseInt(res) + parseInt(this.posting.amount);
    // },

    get_amount_with_tax(clause) {
      // let per = clause == "Food" ? 5 : 12;
      let per = 0;
      if (clause == "Food") {
        per = 5;
      } else if (clause == "Mesentery" || clause == "Bed") {
        per = 12;
      }

      let res = this.getPercentage(this.posting.amount || 0, per);
      let gst = parseFloat(res) / 2;
      this.posting.sgst = gst;
      this.posting.cgst = gst;
      this.posting.tax = res;
      this.posting.amount_with_tax =
        parseFloat(res) + parseFloat(this.posting.amount || 0);
    },

    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },

    get_check_out() {
      this.checkOutDialog = true;
      this.get_transaction();
    },

    get_transaction() {
      let id = this.bookingId;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios
        .get(`get_transaction_by_booking_id/${id}`, payload)
        .then(({ data }) => {
          this.transactions = data.transactions;
          this.totalTransactionAmount = data.totalTransactionAmount;
        });
    },

    get_data(jsEvent = null) {
      let payload = {
        params: {
          id: this.evenIid,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_booking`, payload).then(({ data }) => {
        this.checkData = data;
        this.bookingId = data.id;
        this.checkData.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.customerId = data.customer_id;
        this.show_context_menu(jsEvent);
        if (this.isDbCLick) {
          this.get_event_by_db_click();
        }
      });
    },

    get_event_by_mouse_hover(id, jsEvent) {
      this.evenIid = id;
      this.tx = jsEvent.clientX;
      this.ty = jsEvent.clientY;
      this.$nextTick(() => {
        this.showTooltip = true;
      });
      this.get_data();
    },

    get_event_by_db_click() {
      this.$router.push(`/customer/details/${this.bookingId}`);
    },

    create_reservation(e, obj) {
      this.get_room_types(e, obj);
    },

    get_room_types(e, obj) {
      this.reservation.isCalculate = true;

      this.reservation.room_id = this.RoomList.find(
        (e) => e.room_no == obj.room_no
      ).id;

      this.reservation.room_type = obj.room_type;
      this.reservation.room_no = obj.room_no;
      this.reservation.check_in = e.startStr;
      // this.reservation.check_out = this.convert_checkout_date_format(
      //   new Date(e.endStr)
      // ); //this.convert_date_format(e.end);

      this.reservation.check_out = e.endStr; //this.convert_date_format(e.end);

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: obj.room_type,
          room_no: obj.room_no,
        },
      };
      this.$axios.get(`get_data_by_select`, payload).then(({ data }) => {
        this.reservation.room_id = data.id;
        this.reservation.price = data.room_type.price;

        let commitObj = {
          ...this.reservation,
        };
        this.$store.commit("reservation", commitObj);
        this.$router.push(`/hotel/new2`);
      });
    },

    convert_checkout_date_format(val) {
      const previous = new Date(val.getTime());
      previous.setDate(val.getDate() - 1);
      const date = new Date(previous);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return [year, month, day].join("-");
    },

    viewBillingDialog() {
      let id = this.bookingId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_remaining(val) {
      // let total = this.checkData.total_price;
      let total = this.checkData.remaining_price;
      let advance_price = val;
      // this.checkData.remaining_price = total - advance_price;
    },

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_list`, payload).then(({ data }) => {
        this.calendarOptions.resources = data;
        this.RoomList = data;
      });
    },

    get_posting() {
      let id = this.evenIid;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`posting/${id}`, payload).then(({ data }) => {
        this.postings = data;
      });
    },

    convert_date_format(val) {
      return new Date(val - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },

    get_next_day(val) {
      const tomorrow = new Date(val);
      tomorrow.setDate(tomorrow.getDate() + 1);
      let tmrew = tomorrow.toISOString().substr(0, 10);
      console.log(tmrew);
      return tmrew;
    },

    convert_end_date_format(start, end) {
      console.log(end);
      if (end == null) {
        return this.get_next_day(start);
      }
      return this.get_next_day(end);

      return new Date(end - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },

    store_check_out() {
      // if (this.checkData.full_payment == "") {
      //   alert("enter full payment");
      //   return true;
      // }

      this.loading = true;
      let payload = {
        booking_id: this.checkData.id,
        remaining_price: this.checkData.remaining_price,
        full_payment: this.checkData.full_payment,
        payment_mode_id: this.checkData.payment_mode_id,
      };
      // return;
      this.$axios
        .post("/check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, true);
            this.redirect_to_invoice(data.data);
          }
        })
        .catch((e) => console.log(e));
    },

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `http://127.0.0.1:8000/api/invoice/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    store_advance(data) {
      if (this.new_advance == "") {
        alert("Enter advance amount");
        return;
      }
      // this.loading = true;
      let payload = {
        new_advance: this.new_advance,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
        company_id: this.$auth.user.company.id,
      };
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, false, true);
          }
        })
        .catch((e) => console.log(e));
    },

    store_check_in(data) {
      if (
        // this.new_payment == "" ||
        // this.new_payment == 0 ||
        data.document ? "" : this.document == null
      ) {
        alert("Enter required fields");
        return;
      }
      this.loading = true;
      let bookingId = data.id;
      let payload = {
        new_payment: this.new_payment,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
      };
      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, true, false);
            data.document ? "" : this.store_document(bookingId);
          }
        })
        .catch((e) => console.log(e));
    },

    store_document(id) {
      let payload = new FormData();
      payload.append("document", this.document);
      payload.append("booking_id", id);
      this.$axios
        .post("/store_document", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          }
        })
        .catch((e) => console.log(e));
    },

    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
    },

    setAvailable() {
      let payload = {
        // cancel_by: this.$auth.user.id,
      };

      this.$axios
        .post(`set_available/${this.bookingId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    setMaintenance() {
      let payload = {
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`set_maintenance/${this.bookingId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    store_posting() {
      let rule =
        Object.keys(this.posting.item).length == 0 ||
        Object.keys(this.posting.amount).length == 0 ||
        Object.keys(this.posting.qty).length == 0 ||
        Object.keys(this.posting.bill_no).length == 0 ||
        this.posting.tax_type == -1;

      if (rule) {
        alert("Please enter required fields");
        return;
      }
      this.loading = true;
      let per = this.posting.tax_type == "Food" ? 5 : 12;
      let payload = {
        ...this.posting,
        booked_room_id: this.evenIid,
        company_id: this.$auth.user.company.id,
        booking_id: this.checkData.id,
        room_id: this.checkData.room_id,
        room: this.checkData.room_no,
        tax_type: per,
      };

      this.$axios
        .post("/posting", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, true);
          }
        })
        .catch((e) => console.log(e));
    },

    cancelItem() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }

      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`cancel_room/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    change_room_by_drag(obj) {
      this.$axios
        .post("/change_room_by_drag", obj)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
            this.get_events();
          }
        })
        .catch((e) => console.log(e));
    },

    change_date_by_drag(obj) {
      // return;
      this.$axios
        .post("/change_date_by_drag", obj)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
            this.get_events();
          }
        })
        .catch((e) => console.log(e));
    },

    close() {
      this.checkInDialog = false;
      this.new_payment = 0;
      this.checkOutDialog = false;
    },

    closePosting() {
      this.postingDialog = false;
      this.posting = {
        item: "",
        qty: "",
        amount: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1,
      };
    },

    validate_payment() {
      if (
        this.checkData.advance_price == 0 ||
        this.checkData.advance_price == ""
      ) {
        alert("enter advance price ");
        return true;
      }
      if (this.checkData.payment_mode_id == "") {
        alert("select payment mode");
        return true;
      }
      return false;
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },

    succuss(
      data,
      check_in = false,
      posting = false,
      check_out = false,
      advance_payment = false
    ) {
      if (check_in) {
        this.checkData = {};
        this.checkInDialog = false;
      }
      if (check_out) {
        this.checkData = {};
        this.checkOutDialog = false;
      }
      if (posting) {
        this.posting = {};
        this.postingDialog = false;
      }

      if (advance_payment) {
        this.checkData = {};
        this.payingAdvance = false;
      }

      this.get_events();
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;
    },
  },
};
</script>

<style scoped>
.app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}

.portrait.v-card {
  margin: 0 auto;
  max-width: 600px;
  width: 100%;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #e9e9e9;
}
td,
th {
  text-align: left;
  padding: 8px;
  border: 1px solid #e9e9e9;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.bg-background {
  background-color: #34444c !important;
}

.bg-background th,
td {
  border-top: none !important;
  border-right: none !important;
  border-left: none !important;
}
.fc-license-message {
  display: none !important;
  z-index: 99999 !important;
  position: absolute !important;
  bottom: 10px !important;
  left: 1px !important;
  background: rgb(238, 238, 238) !important;
  border-color: rgb(221, 221, 221) !important;
  border-style: solid !important;
  border-width: 1px 1px 0px 0px !important;
  padding: 2px 4px !important;
  font-size: 12px !important;
  border-top-right-radius: 3px !important;
}
</style>
