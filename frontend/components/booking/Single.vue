<template>
  <v-dialog v-model="ViewBookingDialog" max-width="1100">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> View Billing </span>
    </template>
    <v-card class="rounded-md" elevation="0">
      <v-toolbar :color="`grey lighten-3`" flat dense>
        <v-tabs
          v-model="activeTab"
          background-color="grey lighten-3"
          align-with-title
        >
          <div class="pa-3">
            <span> On-premises Guest</span>
          </div>
          <div style="padding-left: 80px; padding-top: 12px">
            <span> Reservation #: {{ booking.reservation_no }}</span>
          </div>
          <v-spacer></v-spacer>
          <v-tab v-for="item in itemsCustomer" :key="item">
            {{ item }}
          </v-tab>
        </v-tabs>
        <v-icon class="grey lighten-3 pa-0" @click="ViewBookingDialog = false">
          mdi-close
        </v-icon>
      </v-toolbar>
      <v-container fluid>
        <v-tabs-items v-model="activeTab">
          <v-tab-item class="px-3 py-4">
            <v-row>
              <v-col md="2" cols="12" class="text-center">
                <v-row>
                  <v-col cols="12">
                    <v-avatar tile size="130">
                      <v-card>
                        <img
                          @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
                          class="zoom-on-hover"
                          style="z-index: 1;width: 100%;"
                          :src="
                            booking?.customer?.captured_photo ||
                            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                          "
                        />
                          <!-- <div class="text-right pa-2">
                              <v-icon
                                color="white"
                               
                                >mdi-eye</v-icon
                              >
                            </div> -->
                      </v-card>
                    </v-avatar>

                    <ViewBox
                      ref="ViewBox"
                      :id="$route.params.id"
                      :customer="booking.customer"
                    />
                  </v-col>
                  <v-col cols="12">
                    <div style="display: flex">
                      <v-img
                        class="zoom-on-hover"
                        @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
                        :src="booking?.customer?.id_frontend_side || '/idf.png'"
                        style="margin: 0 auto; width: 35px; height: 35px"
                        contain
                      ></v-img>
                      <v-img
                        class="zoom-on-hover"
                        @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
                        :src="booking?.customer?.id_backend_side || '/idb.png'"
                        style="margin: 0 auto; width: 35px; height: 35px"
                        contain
                      ></v-img>
                    </div>
                  </v-col>
                  <v-col>
                    <table style="width: 100%; border-collapse: collapse">
                      <tr>
                        <td
                          class="text-left"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>Room:</small>
                        </td>
                        <td
                          class="text-right"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>
                            {{
                              transactionSummary &&
                              $utils.currency_format(
                                transactionSummary.sumDebit -
                                  transactionSummary.tot_posting
                              )
                            }}</small
                          >
                        </td>
                      </tr>
                      <tr>
                        <td
                          class="text-left"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>Posting:</small>
                        </td>
                        <td
                          class="text-right"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>
                            {{
                              transactionSummary &&
                              $utils.currency_format(
                                transactionSummary.tot_posting
                              )
                            }}</small
                          >
                        </td>
                      </tr>
                      <tr>
                        <td
                          class="text-left"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small> Total: </small>
                        </td>
                        <td
                          class="text-right"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>
                            {{
                              transactionSummary &&
                              $utils.currency_format(
                                transactionSummary.sumDebit
                              )
                            }}</small
                          >
                        </td>
                      </tr>
                      <tr>
                        <td
                          class="text-left"
                          style="border-bottom: 1px solid #ccc"
                        >
                          Paid:
                        </td>
                        <td
                          class="text-right red--text"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>
                            -{{
                              transactionSummary &&
                              $utils.currency_format(
                                transactionSummary.sumCredit
                              )
                            }}
                          </small>
                        </td>
                      </tr>

                      <tr>
                        <td
                          class="text-left"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small>Balance:</small>
                        </td>
                        <td
                          class="text-right"
                          style="border-bottom: 1px solid #ccc"
                        >
                          <small class="primary--text">
                            {{
                              transactionSummary &&
                              $utils.currency_format(transactionSummary.balance)
                            }}</small
                          >
                        </td>
                      </tr>
                    </table>
                  </v-col>
                </v-row>
              </v-col>

              <v-col
                md="10"
                cols="12"
                v-if="booking && booking.customer && booking.customer.id"
              >
                <v-row>
                  <v-col cols="4">
                    <v-text-field
                      label="Full Name"
                      v-model="booking.title"
                      readonly
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="4">
                    <v-text-field
                      label="Mobile"
                      v-model="booking.customer.contact_no"
                      readonly
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="4">
                    <v-text-field
                      label="Whatsapp"
                      v-model="booking.customer.whatsapp"
                      readonly
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="4">
                    <v-text-field
                      label="Reservation No"
                      v-model="booking.reservation_no"
                      readonly
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="4">
                    <v-text-field
                      label="Number of Rooms"
                      readonly
                      v-model="bookedRooms.length"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="4">
                    <v-text-field
                      label="Booking Date"
                      readonly
                      v-model="booking.booking_date"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="Purpose"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.purpose"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="9"
                    v-if="booking && booking.booking_type == 'room'"
                  >
                    <v-row>
                      <v-col>
                        <v-text-field
                          label="Check In"
                          readonly
                          v-model="booking.check_in_date"
                          dense
                          outlined
                          hide-details
                        ></v-text-field>
                      </v-col>
                      <v-col>
                        <v-text-field
                          label="Check Out"
                          readonly
                          v-model="booking.check_out_date"
                          dense
                          outlined
                          hide-details
                        ></v-text-field>
                      </v-col>
                      <v-col>
                        <v-text-field
                          label="Nights"
                          readonly
                          v-model="booking.total_days"
                          dense
                          outlined
                          hide-details
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-col>

                  <v-col cols="9" v-else>
                    <v-row>
                      <v-col cols="4">
                        <v-text-field
                          label="Event Start"
                          readonly
                          v-model="booking.hall_check_in_date"
                          dense
                          outlined
                          hide-details
                        ></v-text-field>
                      </v-col>
                      <v-col cols="4">
                        <v-text-field
                          label="Event End"
                          readonly
                          v-model="booking.hall_check_out_date"
                          dense
                          outlined
                          hide-details
                        ></v-text-field>
                      </v-col>
                      <v-col cols="4">
                        <v-text-field
                          label="Total Hours"
                          readonly
                          v-model="bookedRooms[0].total_booking_hours"
                          dense
                          outlined
                          hide-details
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="Pay Type"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="getRelatedPaidBy"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="Reach By"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.customer.customer_type"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="Source"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.source"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="Reference Number"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.reference_no"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="Country"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.customer.country"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="State"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.customer.state"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="City"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.customer.city"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="3">
                    <v-text-field
                      label="City"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.customer.zip_code"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      label="Guest Request"
                      dense
                      outlined
                      hide-details
                      readonly
                      v-model="booking.request"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item class="px-3 py-4">
            <CustomerRooms :booking="booking" :orderRooms="orderRooms" />
          </v-tab-item>
          <v-tab-item class="px-3 py-4">
            <CustomerPostings :full_name="booking.title" :postings="postings" />
          </v-tab-item>
          <v-tab-item class="px-3 pt-5">
            <CustomerTransactions
              :transactions="transactions"
              :totalTransactionAmount="totalTransactionAmount"
            />
          </v-tab-item>

          <v-tab-item class="px-3 py-4">
            <v-row>
              <v-col md="12" cols="12">
                <v-row class="mt-4">
                  <v-col md="3">
                    <div class="text-box" style="float: left">
                      <h6>Online Payment Status</h6>
                      <p
                        style="color: green; font-weight: bold"
                        v-if="
                          booking &&
                          booking.online_payment_response &&
                          booking.online_payment_response
                            .razorpay_payment_link_status == 'paid'
                        "
                      >
                        Success
                      </p>
                      <p v-else style="color: red; font-weight: bold">Failed</p>
                    </div>
                  </v-col>
                  <v-col md="3">
                    <div class="text-box" style="float: left">
                      <h6>Online Booking Reference Number</h6>
                      <p>
                        {{
                          (booking &&
                            booking.widget_confirmation_number &&
                            booking.widget_confirmation_number.split("_")[1]) ||
                          "---"
                        }}
                      </p>
                    </div>
                  </v-col>

                  <v-col md="3">
                    <div class="text-box" style="float: left">
                      <h6>RazorPay - Reference Id</h6>
                      <p>
                        {{ (booking && booking.payment_reference_id) || "---" }}
                      </p>
                    </div>
                  </v-col>
                  <v-col md="3">
                    <div class="text-box" style="float: left">
                      <h6>RazorPay - Payment Id</h6>
                      <p>
                        {{
                          (booking &&
                            booking.online_payment_response &&
                            booking.online_payment_response
                              .razorpay_payment_id) ||
                          "---"
                        }}
                      </p>
                    </div>
                  </v-col>
                  <!-- <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>RazorPay - Razorpay Order Id</h6>
                        <p>
                          (booking &&
                              booking.online_payment_response &&
                              booking.online_payment_response.razorpay_payment_id) ||
                            "---"
                        </p>
                      </div>
                    </v-col> -->
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
      </v-container>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["BookingId"],
  data: () => ({
    ViewBookingDialog: false,
    roomTypeColor: "grey lighten-3",
    discount: 0,
    hallRentPerHour: 0,
    projecterTotalAmount: 0,
    cleaningTotalAmount: 0,
    electricityTotalAmount: 0,
    audioTotalAmount: 0,
    tax_percentage: 0,
    room_tax_amount: 0,
    hallItemsTotal: 0,

    durationInHours: 1,
    foodTotalAmount: 0,

    hallRentTotalAmount: 0,
    otherCharges: 0,

    AmountGrandTotal: 0,
    hallTaxableTotalAmount: 0,
    inv_total_without_tax: 0,
    inv_total: 0,
    inv_total_tax: 0,
    discount: 0,

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    documentObj: {
      fileExtension: null,
      file: null,
    },
    options: {},
    imgView: false,
    showImage: "",
    Model: "Customer",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    payData: {},
    loading: false,
    response: "",
    customer: [],
    itemsCustomer: [],
    tab1: null,
    activeTab: 0,

    tab: null,
    payments: [],
    booking: [],
    bookedRooms: [],
    orderRooms: [],
    postings: [],
    transactions: [],
    transactionSummary: [],
    errors: [],
    totalAmount: 0,
    totalPostingAmount: 0,
    totalTransactionAmount: 0,
    room_category_type: "",
    food: [],
    extra_amounts: [],
  }),

  computed: {
    getRelatedPaidBy() {
      return this.booking && this.booking.paid_by == 2
        ? "Paid By Agent"
        : "Paid By Guest";
    },
  },
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() {},

  methods: {
    closeDialogs() {
      this.getData();
    },

    calTotalAmount(payments) {
      let sum = 0;
      payments.forEach((item) => {
        sum += parseFloat(item.amount);
      });
      this.totalAmount = sum;
    },

    redirect() {
      this.$router.push("/");
    },

    getData() {
      let id = this.BookingId;
      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        this.totalTransactionAmount = data.totalTransactionAmount;
        this.transactions = data.transaction;
        this.transactionSummary = data.transactionSummary;
        const booking = data.booking;
        this.customer = booking.customer;
        this.booking = booking;

        this.booking.online_payment_response = JSON.parse(
          booking.payment_response
        );
        this.payments = booking.payments;
        this.bookedRooms = booking.booked_rooms;
        this.orderRooms = booking.order_rooms;
        this.postings = data.postings;
        //end booking
        this.loading = false;
        this.showImage = data.booking.customer.image;
        this.calTotalAmount(this.payments);
        this.room_category_type = data.booking.room_category_type;

        this.itemsCustomer = [
          "Reservation",
          booking.booking_type == "hall" ? "Hall" : "Room",
          "Postings",
          "Transaction",
          "Online Booking",
        ];

        return;

        if (this.room_category_type == "Hall") {
          this.roomTypeColor = "green";
          this.itemsCustomer = [
            "Reservation",
            "Room",
            "Postings",
            "Transaction",
            "Food",
            "Price List",
          ];
          if (data.booking.hall_booking)
            this.food = data.booking.hall_booking.food;
          this.extra_amounts = data.booking.hall_booking.extra_amounts;

          this.hallRentTotalAmount = data.booking.hall_booking.hall_rent_amount;
          this.electricityTotalAmount =
            data.booking.hall_booking.hall_electricity_amount;

          this.audioTotalAmount = data.booking.hall_booking.hall_audio_system;
          this.projecterTotalAmount =
            data.booking.hall_booking.hall_projector_amount;
          this.cleaningTotalAmount =
            data.booking.hall_booking.hall_cleaning_charges;
          this.foodTotalAmount = data.booking.hall_booking.food_total_amount;
          this.otherCharges =
            data.booking.hall_booking.hall_extra_amounts_total;
          this.inv_total_tax = data.booking.hall_booking.inv_total_tax;
          this.inv_total_without_tax =
            data.booking.hall_booking.inv_total_without_tax;
          this.inv_total = data.booking.hall_booking.inv_total;
          this.discount = data.booking.hall_booking.discount;

          this.hallItemsTotal =
            parseFloat(this.hallRentTotalAmount) +
            parseFloat(this.electricityTotalAmount) +
            parseFloat(this.audioTotalAmount) +
            parseFloat(this.projecterTotalAmount) +
            parseFloat(this.cleaningTotalAmount) +
            parseFloat(this.foodTotalAmount) +
            parseFloat(this.otherCharges);
        } else {
          this.roomTypeColor = "primary";

          if (data.bookingwidget_confirmation_number != "") {
            this.itemsCustomer = [
              "Reservation",
              "Room",
              "Postings",
              "Transaction",
              "Online Booking",
            ];
          } else {
            this.itemsCustomer = [
              "Reservation",
              "Room",
              "Postings",
              "Transaction",
            ];
          }
        }
      });
    },
  },
};
</script>
