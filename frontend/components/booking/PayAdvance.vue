<template>
  <v-dialog v-model="checkOutDialog" persistent max-width="850px">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> Payment </span>
    </template>
    <v-card>
      <v-alert dense class="grey lighten-3 primary--text">
        <v-row>
          <v-col> <div style="font-size: 18px">Payment</div> </v-col>
          <v-col>
            <div class="text-right">
              <v-icon @click="checkOutDialog = false" color="primary"
                >mdi-close-circle</v-icon
              >
            </div>
          </v-col>
        </v-row>
      </v-alert>
      <v-card-text>
        <v-row no-gutter v-if="BookingData && BookingData.id">
          <v-col cols="6" class="text-center">
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-avatar size="150" class="mb-1">
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
                <!-- <v-col cols="12">
            <v-textarea
              rows="2"
              v-model="customer_full_address"
              readonly
              label="Address"
              outlined
              dense
              hide-details
            ></v-textarea>
          </v-col> -->
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
                <v-col cols="12" class="pt-10">
                  <!-- <pre>{{ roomData }}</pre> -->

                  <table style="width: 100%">
                    <tr>
                      <td
                        class="text-left"
                        style="width: 110px; border-bottom: 1px solid #eeeeee"
                      >
                        Room
                      </td>
                      <td
                        style="width: 110px; border-bottom: 1px solid #eeeeee"
                      >
                        {{ $utils.currency_format(roomData.price) }}
                      </td>
                      <td colspan="2" class="text-center">Total Rs.</td>
                    </tr>
                    <tr>
                      <td class="text-left">Posting</td>
                      <td>
                        {{
                          $utils.currency_format(
                            BookingData.total_posting_amount
                          )
                        }}
                      </td>
                      <td colspan="2" class="text-center">
                        <span style="font-size: 18px" class="blue--text">{{
                          $utils.currency_format(
                            parseFloat(roomData.price) +
                              parseFloat(BookingData.total_posting_amount)
                          )
                        }}</span>
                      </td>
                    </tr>
                  </table>
                  <v-divider></v-divider>
                  <table style="width: 100%">
                    <tr>
                      <td
                        class="text-left"
                        style="width: 110px; border-bottom: 1px solid #eeeeee"
                      >
                        Paid
                      </td>
                      <td
                        style="width: 110px; border-bottom: 1px solid #eeeeee"
                      >
                        {{ $utils.currency_format(BookingData.paid_amounts) }}
                      </td>
                      <td colspan="2" class="text-center">Balance Rs.</td>
                    </tr>
                    <tr>
                      <td class="text-left">Others</td>
                      <td>
                        {{ $utils.currency_format(0) }}
                      </td>
                      <td colspan="2" class="text-center">
                        <span style="font-size: 18px" class="red--text">{{
                          $utils.currency_format(remaining_price)
                        }}</span>
                      </td>
                    </tr>
                  </table>
                </v-col>
              </v-row>
            </v-container>
          </v-col>
          <v-divider vertical></v-divider>
          <v-col cols="6">
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-card elevation="0">
                    <v-container>
                      <v-row>
                        <v-col cols="12">
                          <Heading class="mb-3" label="Transactions" />
                          <table style="width: 100%">
                            <tr style="font-size: 13px">
                              <td
                                class="text-center primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                Date
                              </td>
                              <td
                                class="text-center primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                Debit
                              </td>
                              <td
                                class="text-center primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                Credit
                              </td>
                              <td
                                class="text-center primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                Balance
                              </td>
                              <td
                                class="text-center primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                Receipt
                              </td>
                            </tr>

                            <tr
                              style="font-size: 13px"
                              v-for="(item, index) in transactions"
                              :key="index"
                            >
                              <td
                                class="text-center"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                {{ item.created_at || "---" }}
                              </td>
                              <td
                                class="text-center"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                {{
                                  item && item.debit == 0
                                    ? "---"
                                    : $utils.currency_format(item.debit)
                                }}
                              </td>
                              <td
                                class="text-center"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                {{
                                  item && item.credit == 0
                                    ? "---"
                                    : $utils.currency_format(item.credit)
                                }}
                              </td>
                              <td
                                class="text-center"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                {{
                                  $utils.currency_format(item.balance) || "---"
                                }}
                              </td>
                              <td
                                class="text-center blue--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                {{ item.id }}
                              </td>
                            </tr>

                            <tr style="font-size: 13px">
                              <td
                                colspan="3"
                                class="text-right primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                Total Balance
                              </td>
                              <td
                                colspan="2"
                                class="text-left pl-3 primary--text"
                                style="
                                  width: 110px;
                                  border-bottom: 1px solid #eeeeee;
                                "
                              >
                                {{
                                  $utils.currency_format(
                                    totalTransactionAmount + exceedHoursCharges
                                  )
                                }}
                              </td>
                            </tr>
                          </table>
                        </v-col>
                        <v-col cols="12">
                          <v-divider></v-divider>
                        </v-col>
                        <v-col cols="4">
                          <b><Heading label="Payment" /></b>
                        </v-col>
                        <v-col cols="8" class="text-right">
                          <Heading label="Receipt Number: 123456" />
                          <!-- <div>Receipt Number: 123456</div> -->
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
                            label="Pay Amount"
                            outlined
                            dense
                            hide-details
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" class="text-center mt-5">
                          <v-hover v-slot:default="{ hover, props }">
                            <span v-bind="props">
                              <v-btn
                                small
                                :outlined="!hover"
                                rounded
                                color="red"
                                class="white--text"
                                @click="$emit(`close-dialog`)"
                                >Cancel</v-btn
                              >
                            </span>
                          </v-hover>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <v-hover v-slot:default="{ hover, props }">
                            <span v-bind="props">
                              <v-btn
                                small
                                :outlined="!hover"
                                rounded
                                color="green"
                                class="white--text"
                                @click="store_advance"
                                >Submit</v-btn
                              >
                            </span>
                          </v-hover>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card>
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

      this.tempBalance = remaining_price;

      this.actualCheckoutTime = this.roomData.check_out_time;

      this.calculateHoursQty(this.actualCheckoutTime);
      this.get_transaction();
    }
  },
  computed: {
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

    store_advance() {
      // let full_payment = parseFloat(this.full_payment);
      // if (full_payment <= 0) {
      //   this.alert("Warning", "Payment should be greater than zero","error");
      //   return;
      // }
      let payload = {
        new_advance: parseFloat(this.full_payment),
        reference_number: this.reference,
        booking_id: this.BookingData.id,
        remaining_price: this.remaining_price,
        payment_mode_id: this.payment_mode_id,
        company_id: this.$auth.user.company.id,
        user_id: this.$auth.user.id,
      };

      // this.loading = true;
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;
            this.$emit("close-dialog");
            this.$swal("Success!", "Room has been checked out", "success");
          }
        })
        .catch((e) => console.log(e));
    },

    closeDialog(payload) {
      this.discount = 0;
      this.full_payment = 0;
      this.$emit("close-dialog", payload);
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
  },
};
</script>

<!-- <style scoped src="@/assets/css/checkout.css"></style> -->
