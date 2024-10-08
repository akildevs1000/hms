<template>
  <v-dialog v-model="checkOutDialog" persistent max-width="900px">
    <AssetsIconClose left="890" @click="checkOutDialog = false" />
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> Check Out </span>
    </template>
    <v-card>
      <v-alert dense class="grey lighten-3 primary--text">
        <v-row>
          <v-col> <div style="font-size: 18px">Check Out</div> </v-col>
          <v-col class="text-right">
            <div style="font-size: 18px; color: #1402f7">
              Reservation # {{ BookingData.reservation_no }}
            </div>
          </v-col>
        </v-row>
      </v-alert>
      <v-card-text>
        <v-row no-gutter v-if="BookingData && BookingData.id">
          <v-col cols="6" class="text-center">
            <v-row no-gutter>
              <v-col cols="12" class="text-center">
                <v-avatar size="125">
                  <img
                    class="pa-2"
                    style="border: 1px solid grey"
                    :src="
                      roomData?.customer?.captured_photo ||
                      'https://i.pinimg.com/474x/e4/c5/9f/e4c59fdbb41ccd0f87dc0be871d91d98.jpg'
                    "
                    alt="Profile Image"
                  />
                </v-avatar>
              </v-col>
            </v-row>
            <v-container v-if="!isGroupBooking">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="roomData.customer.full_name"
                    readonly
                    label="Full Name"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="roomData.customer.contact_no"
                    readonly
                    label="Phone Number"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    rows="2"
                    v-model="roomData.booking.source"
                    readonly
                    label="Source"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="roomData.checkin_datetime_only"
                    readonly
                    label="Check IN"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="roomData.checkout_datetime_only"
                    readonly
                    label="Check Out"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="BookingData.room_no"
                    readonly
                    label="Room Number"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12" class="pt-10">
                  <v-row no-gutter>
                    <v-col cols="8">
                      <table style="width: 100%">
                        <tr>
                          <td
                            class="text-left border-bottom"
                            style="width: 110px"
                          >
                            Room
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{
                              isGroupBooking
                                ? "Group Booking"
                                : $utils.currency_format(roomData.price)
                            }}
                          </td>
                        </tr>
                        <tr>
                          <td
                            class="text-left border-bottom"
                            style="width: 110px"
                          >
                            Posting
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{
                              $utils.currency_format(
                                BookingData.total_posting_amount
                              )
                            }}
                          </td>
                        </tr>
                        <tr>
                          <td
                            class="text-left border-bottom"
                            style="width: 110px"
                          >
                            Total
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{ $utils.currency_format(roomData.grand_total) }}
                          </td>
                          <!-- <td colspan="2" class="text-center">Total Rs.</td> -->
                        </tr>
                        <tr>
                          <td
                            class="text-left border-bottom red--text"
                            style="width: 110px"
                          >
                            Paid
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{
                              $utils.currency_format(BookingData.paid_amounts)
                            }}
                          </td>
                          <!-- <td colspan="2" class="text-center">Balance Rs.</td> -->
                        </tr>
                      </table>
                    </v-col>
                    <v-col class="pt-8">
                      <table>
                        <tr>
                          <td class="text-center">Total Rs.</td>
                        </tr>
                        <tr>
                          <td class="text-center">
                            <span style="font-size: 18px" class="blue--text">
                              {{
                                $utils.currency_format(
                                  parseFloat(roomData.grand_total)
                                )
                              }}
                            </span>
                          </td>
                        </tr>
                      </table>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-container>
            <v-container v-else>
              <v-row v-if="sub_customer && sub_customer.id">
                <v-col cols="12">
                  <v-text-field
                    v-model="sub_customer.first_name"
                    readonly
                    label="First Name"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="sub_customer.last_name"
                    readonly
                    label="Last Name"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="sub_customer.contact_no"
                    readonly
                    label="Phone Number"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="roomData.checkin_datetime_only"
                    readonly
                    label="Check IN"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="roomData.checkout_datetime_only"
                    readonly
                    label="Check Out"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="BookingData.room_no"
                    readonly
                    label="Room Number"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12" class="pt-10">
                  <v-row no-gutter>
                    <v-col cols="8">
                      <table style="width: 100%">
                        <tr>
                          <td
                            class="text-left border-bottom"
                            style="width: 110px"
                          >
                            Room
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{
                              isGroupBooking
                                ? "Group Booking"
                                : $utils.currency_format(roomData.price)
                            }}
                          </td>
                        </tr>
                        <tr>
                          <td
                            class="text-left border-bottom"
                            style="width: 110px"
                          >
                            Posting
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{ $utils.currency_format(guestPostingAmount) }}
                          </td>
                        </tr>
                        <tr>
                          <td
                            class="text-left border-bottom red--text"
                            style="width: 110px"
                          >
                            Discount
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{
                              $utils.currency_format(posting_payment.discount)
                            }}
                          </td>
                          <!-- <td colspan="2" class="text-center">Balance Rs.</td> -->
                        </tr>
                        <tr>
                          <td
                            class="text-left border-bottom red--text"
                            style="width: 110px"
                          >
                            Paid
                          </td>
                          <td
                            class="text-right border-bottom"
                            style="width: 110px"
                          >
                            {{ $utils.currency_format(posting_payment.paid) }}
                          </td>
                          <!-- <td colspan="2" class="text-center">Balance Rs.</td> -->
                        </tr>
                      </table>
                    </v-col>
                    <v-col class="pt-8">
                      <table>
                        <tr>
                          <td class="text-center">Total Rs.</td>
                        </tr>
                        <tr>
                          <td class="text-center">
                            <span style="font-size: 18px" class="blue--text">
                              {{
                                $utils.currency_format(
                                  parseFloat(setInitialBalance)
                                )
                              }}
                            </span>
                          </td>
                        </tr>
                      </table>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-container>
          </v-col>
          <v-divider vertical></v-divider>
          <v-col cols="6">
            <v-container>
              <v-row>
                <v-col cols="12" v-if="!isGroupBooking">
                  <Heading class="mb-3" label="Transactions" />
                  <table style="width: 100%">
                    <tr style="font-size: 13px">
                      <td
                        class="text-center primary--text border-bottom"
                        style="width: 110px"
                      >
                        Date
                      </td>
                      <td
                        class="text-right primary--text border-bottom"
                        style="width: 110px"
                      >
                        Debit
                      </td>
                      <td
                        class="text-right primary--text border-bottom"
                        style="width: 110px"
                      >
                        Credit
                      </td>
                      <td
                        class="text-right primary--text border-bottom"
                        style="width: 110px"
                      >
                        Balance
                      </td>
                    </tr>

                    <tr
                      style="font-size: 13px"
                      v-for="(item, index) in transactions"
                      :key="index"
                    >
                      <td
                        class="text-center border-bottom"
                        style="width: 110px"
                      >
                        {{ item.created_at || "---" }}
                      </td>
                      <td class="text-right border-bottom" style="width: 110px">
                        {{
                          item && item.debit == 0
                            ? "---"
                            : $utils.currency_format(item.debit)
                        }}
                      </td>
                      <td class="text-right border-bottom" style="width: 110px">
                        {{
                          item && item.credit == 0
                            ? "---"
                            : $utils.currency_format(item.credit)
                        }}
                      </td>
                      <td class="text-right border-bottom" style="width: 110px">
                        {{ $utils.currency_format(item.balance) || "---" }}
                      </td>
                    </tr>

                    <tr style="font-size: 13px">
                      <td
                        colspan="3"
                        class="text-right primary--text"
                        style="width: 110px"
                      >
                        Total Balance
                      </td>
                      <td
                        class="text-right pl-3 primary--text"
                        style="width: 110px"
                      >
                        {{ $utils.currency_format(totalTransactionAmount) }}
                      </td>
                    </tr>
                  </table>
                </v-col>
                <v-col v-else>
                  <Heading class="mb-3" label="Postings" />
                  <BookingPostingList
                    :room_id="roomData && roomData.id"
                  ></BookingPostingList>
                </v-col>
                <v-col cols="12">
                  <v-divider></v-divider>
                </v-col>
                <v-col cols="12" v-if="!isGroupBooking">
                  <v-card outlined>
                    <v-container>
                      <v-row>
                        <v-col cols="6">
                          <Heading label="Payment" />
                        </v-col>
                        <v-col cols="6" class="text-right">
                          <v-icon
                            small
                            color="primary"
                            @click="redirect_to_invoice(roomData.booking_id)"
                            >mdi-printer</v-icon
                          >
                          &nbsp;
                          <v-icon
                            small
                            color="primary"
                            @click="redirect_to_invoice(roomData.booking_id)"
                            >mdi-download</v-icon
                          >
                        </v-col>
                        <v-col cols="4">
                          <v-autocomplete
                            label="Mode"
                            v-model="payment_mode_id"
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
                            hide-details
                          ></v-autocomplete>
                        </v-col>
                        <v-col cols="8">
                          <v-text-field
                            label="Reference"
                            dense
                            outlined
                            type="text"
                            v-model="reference"
                            hide-details
                          ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                          <v-text-field
                            v-model="tempBalance"
                            label="Balance"
                            outlined
                            dense
                            hide-details
                            @keyup="setNewBalance(tempBalance, discount)"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                          <v-text-field
                            v-model="discount"
                            label="Discount"
                            outlined
                            dense
                            hide-details
                            @keyup="setNewBalance(tempBalance, discount)"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                          <v-text-field
                            v-model="full_payment"
                            label="New Balance"
                            outlined
                            dense
                            hide-details
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" class="text-center mt-5">
                          <AssetsButtonCancel @click="$emit(`close-dialog`)" />
                          &nbsp; &nbsp;
                          <AssetsButtonSubmit />
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card>
                </v-col>
                <v-col cols="12" v-else>
                  <v-card outlined>
                    <v-container>
                      <v-row>
                        <v-col cols="6">
                          <Heading label="Payment (Postings)" />
                        </v-col>
                        <v-col cols="6" class="text-right">
                          <!-- <v-icon
                            small
                            color="primary"
                            @click="redirect_to_invoice(roomData.booking_id)"
                            >mdi-printer</v-icon
                          >
                          &nbsp;
                          <v-icon
                            small
                            color="primary"
                            @click="redirect_to_invoice(roomData.booking_id)"
                            >mdi-download</v-icon
                          > -->
                        </v-col>
                        <v-col cols="4">
                          <v-autocomplete
                            label="Mode"
                            v-model="postingPaymentPayload.payment_mode"
                            :items="[
                              'Cash',
                              'Card',
                              'Online',
                              'Bank',
                              'UPI',
                              'Cheque',
                            ]"
                            item-text="name"
                            item-value="id"
                            dense
                            outlined
                            hide-details
                          ></v-autocomplete>
                        </v-col>
                        <v-col cols="8">
                          <v-text-field
                            label="Reference"
                            dense
                            outlined
                            type="text"
                            v-model="postingPaymentPayload.reference"
                            hide-details
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4">
                          <v-text-field
                            readonly
                            v-model="setInitialBalance"
                            label="Balance"
                            outlined
                            dense
                            hide-details
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4">
                          <v-text-field
                            v-model="postingPaymentPayload.discount"
                            label="Discount"
                            outlined
                            dense
                            hide-details
                            @keyup="
                              setAfterDiscount(postingPaymentPayload.discount)
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4">
                          <v-text-field
                            readonly
                            v-model="postingPaymentPayload.after_discount"
                            label="After Discount"
                            outlined
                            dense
                            hide-details
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                          <v-text-field
                            v-model="postingPaymentPayload.paid"
                            label="Amount to Pay"
                            outlined
                            dense
                            hide-details
                            @keyup="setNewBalance(postingPaymentPayload)"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" class="text-center mt-5">
                          <AssetsButtonCancel @click="$emit(`close-dialog`)" />
                          &nbsp; &nbsp;
                          <AssetsButtonSubmit
                            :isDisabled="!setInitialBalance"
                            @click="processPostingPayment"
                          />
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card>
                </v-col>
                <v-col cols="12" class="text-center">
                  <div
                    style="position: relative; height: 80px; text-align: center"
                  >
                    <div
                      class=""
                      style="position: absolute; bottom: 0%; width: 100%"
                    >
                      <v-hover v-slot:default="{ hover, props }">
                        <span v-bind="props">
                          <v-btn
                            block
                            small
                            :outlined="!hover"
                            rounded
                            color="green lighten-1"
                            class="white--text"
                            @click="store_check_out"
                          >
                            Check Out
                          </v-btn>
                        </span>
                      </v-hover>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-container>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
const today = new Date();

function formatTime(date) {
  let hours = date.getHours().toString().padStart(2, "0");
  let minutes = date.getMinutes().toString().padStart(2, "0");
  return `${hours}:${minutes}`;
}
export default {
  props: ["BookingData", "roomData"],

  data() {
    return {
      old_balance: 0,
      postingPaymentPayload: {
        paid: 0,
        balance: 0,
        payment_mode: "Cash",
        reference: "REF123456",
        discount: 0,
        after_discount: 0,
      },

      payment_mode_id: 1,
      change_checkout_time: false,

      grand_remaining_price: 0,
      remaining_price: 0,
      Testing: true,
      isHall: false,

      exceedHoursCharges: 0,
      actualCheckoutTime: formatTime(today),
      isDiscount: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      transactions: [],
      totalTransactionAmount: 0,
      full_payment: 0,
      isPrintInvoice: false,
      discount: 0,
      tempBalance: 0,
      reference: "",
      customer: {
        title: "",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        exp_menu: false,
        exp: null,
      },
      after_discount_balance: 0,
      errors: [],

      checkOutDialog: false,
    };
  },

  watch: {
    BookingData() {
      this.discount = 0;
      this.full_payment = 0;
    },
  },
  created() {
    this.preloader = false;
    if (this.roomData && this.roomData.id) {
      let { grand_remaining_price, remaining_price } = this.BookingData;
      this.grand_remaining_price = grand_remaining_price;
      this.remaining_price = remaining_price;
      this.full_payment = remaining_price - this.discount;
      this.after_discount_balance = grand_remaining_price;

      this.actualCheckoutTime = this.roomData.check_out_time;

      this.calculateHoursQty(this.actualCheckoutTime);
      this.get_transaction();
    }
  },
  computed: {
    sub_customer() {
      return this.roomData?.sub_customer_room_history?.sub_customer || null;
    },
    posting_payment() {
      if (this.roomData && this.roomData.posting_payment) {
        return this.roomData.posting_payment;
      }
    },
    guestPostingAmount() {
      return this.roomData.postings.reduce((acc, cur) => {
        return acc + cur.amount_with_tax;
      }, 0);
    },
    setInitialBalance() {
      if (!this.posting_payment) {
        return 0;
      }

      if (this.posting_payment.paid == 0 && this.posting_payment.balance == 0) {
        return this.guestPostingAmount;
      }
      return this.posting_payment.balance;
    },
    isGroupBooking() {
      return this.BookingData.group_name == "yes";
    },
    customer_full_address() {
      let { customer } = this.roomData;

      if (!customer.state) {
        return "---";
      }

      return `${customer.state}, ${customer.city}, ${customer.zip_code}, ${customer.country}`;
    },
  },
  methods: {
    setNewBalance(tempBalance, discount) {
      this.full_payment = parseFloat(tempBalance) - parseFloat(discount);
    },
    get_after_discount_balance(amt = 0) {
      let discount = amt || 0;
      let blc = parseFloat(this.grand_remaining_price) - parseFloat(discount);
      this.after_discount_balance = blc.toFixed(2) || 0;
    },

    store_check_out() {
      // let full_payment = parseFloat(this.full_payment);
      // if (full_payment <= 0) {
      //   this.alert("Warning", "Payment should be greater than zero","error");
      //   return;
      // }
      let payload = {
        booking_id: this.BookingData.id,
        grand_remaining_price: this.grand_remaining_price,
        remaining_price: this.remaining_price,
        full_payment: parseFloat(this.full_payment),
        payment_mode_id: this.payment_mode_id,
        company_id: this.$auth.user.company.id,
        isPrintInvoice: this.isPrintInvoice,
        reference_number: this.reference,
        discount: this.discount,
        user_id: this.$auth.user.id,
        isHall: this.isHall,
        exceedHoursCharges: this.exceedHoursCharges,
        room_id: this.roomData.room_id,
      };

      // this.loading = true;
      this.$axios
        .post("/check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;
            this.$emit("close-dialog");
            this.$swal("Success!", "Room has been checked out", "success").then(
              () => {
                if (this.isPrintInvoice) {
                  this.redirect_to_invoice(data.bookingId);
                }
              }
            );
          }
        })
        .catch((e) => console.log(e));
    },

    closeDialog(payload) {
      this.discount = 0;
      this.full_payment = 0;
      this.$emit("close-dialog", payload);
    },

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${process.env.BACKEND_URL}invoice/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    calculateHoursQty(actualCheckoutTime) {
      let { check_out, extra_hours, extra_booking_hours_charges, room } =
        this.roomData;

      if (room?.room_type?.type !== "hall") {
        this.isHall = false;
        return;
      }

      this.isHall = true;

      const extra_per_hour_charges = extra_booking_hours_charges / extra_hours;

      const start = new Date(check_out);
      let end = new Date(this.getCurrentDate() + " " + actualCheckoutTime);

      // Check if the end time is earlier than the start time, indicating it falls on the next day
      if (end < start) {
        // Add 24 hours (in milliseconds) to the end time
        end = new Date(end.getTime() + 24 * 60 * 60 * 1000);
      }

      const differenceInMs = end - start;

      const totalHours = Math.ceil(differenceInMs / (1000 * 60 * 60));

      this.exceedHoursCharges = Math.round(totalHours) * extra_per_hour_charges;

      this.transactions.push({
        created_at: this.getCurrentDate(),
        debit: this.exceedHoursCharges,
        credit: 0,
        balance: this.exceedHoursCharges,
      });
    },

    get_transaction() {
      let id = this.BookingData.id;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          // isHall: this.isHall,
          // exceedHoursCharges: this.exceedHoursCharges,
          // customer_id: this.BookingData.customer_id,
          // payment_mode_id: this.BookingData.payment_mode_id,
          // reference: this.reference,
          // user_id: this.$auth.user.id,
        },
      };
      this.$axios
        .get(`get_transaction_by_booking_id/${id}`, payload)
        .then(({ data }) => {
          this.transactions = data.transactions;
          this.totalTransactionAmount = data.totalTransactionAmount;
          this.tempBalance = data.totalTransactionAmount;
          this.full_payment = data.totalTransactionAmount;
        });
    },

    getCurrentDate() {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, "0");
      const day = String(now.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },

    setAfterDiscount(discount) {
      this.postingPaymentPayload.after_discount =
        parseFloat(this.setInitialBalance) - parseFloat(discount);
    },
    setNewBalance({ after_discount, paid }) {
      this.postingPaymentPayload.balance =
        parseFloat(after_discount) - parseFloat(paid);
    },
    async processPostingPayment() {
      let payload = {
        booking_id: this.roomData.booking_id,
        room_id: this.roomData.room_id,
        sub_customer_id: this.sub_customer.id,
        ...this.postingPaymentPayload,
      };

      console.log(payload);
      try {
        await this.$axios.post(`posting-payment`, payload);
        alert("Payment has been processed");
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>
