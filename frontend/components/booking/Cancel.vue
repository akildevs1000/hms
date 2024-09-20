<template>
  <v-dialog
    v-model="checkInDialog"
    persistent
    :max-width="isGroupBooking ? '' : '850'"
  >
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> Cancel Room </span>
    </template>
    <v-card>
      <v-alert dense class="grey lighten-3 primary--text">
        <v-row>
          <v-col>
            <div style="font-size: 18px">
              {{ isGroupBooking ? "Group " : "" }} Cancel Room
            </div>
          </v-col>
          <v-col class="text-center">
            <div style="font-size: 18px">
              Reservation # {{ BookingData.reservation_no }}
            </div>
          </v-col>
          <v-col>
            <div class="text-right">
              <AssetsButtonClose @close="checkInDialog = false" />
            </div>
          </v-col>
        </v-row>
      </v-alert>
      <v-card-text>
        <v-row no-gutter v-if="BookingData && BookingData.id">
          <v-col v-if="isGroupBooking" cols="4" class="text-center">
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
            <v-container>
              <v-row class="mt-2">
                <v-col cols="3">
                  <v-autocomplete
                    v-model="guest.title"
                    :items="[
                      { id: 1, name: `Mr` },
                      { id: 2, name: `Mrs` },
                      { id: 3, name: `Miss` },
                      { id: 4, name: `Ms` },
                      { id: 5, name: `Dr` },
                    ]"
                    label="Title *"
                    dense
                    item-text="name"
                    item-value="name"
                    hide-details
                    outlined
                  ></v-autocomplete>
                </v-col>
                <v-col cols="9">
                  <v-row no-gutter>
                    <v-col cols="6">
                      <v-text-field
                        v-model="guest.first_name"
                        label="First Name"
                        outlined
                        dense
                        hide-details
                      ></v-text-field>
                    </v-col>
                    <v-col cols="6">
                      <v-text-field
                        v-model="guest.last_name"
                        label="Last Name"
                        outlined
                        dense
                        hide-details
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    v-model="guest.contact_no"
                    label="Phone Number"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="guest.whatsapp"
                    label="Whatsapp"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="guest.email"
                    label="Last Name"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-menu
                    v-model="dob_menu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="guest.dob"
                        readonly
                        label="DOB"
                        v-on="on"
                        v-bind="attrs"
                        hide-details
                        dense
                        outlined
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      no-title
                      v-model="guest.dob"
                      @input="dob_menu = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="guest.nationality"
                    label="Phone Number"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="guest.country"
                    label="Country"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    v-model="guest.state"
                    label="State"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    v-model="guest.city"
                    label="City"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>

                <v-col cols="4">
                  <v-autocomplete
                    readonly
                    label="Adult"
                    :items="[0, 1, 2, 3]"
                    dense
                    outlined
                    v-model="roomData.no_of_adult"
                    hide-details
                    required
                  ></v-autocomplete>
                </v-col>
                <v-col cols="4">
                  <v-autocomplete
                    readonly
                    label="Child"
                    :items="[0, 1, 2, 3]"
                    dense
                    outlined
                    v-model="roomData.no_of_child"
                    hide-details
                    required
                  ></v-autocomplete>
                </v-col>
                <v-col cols="4">
                  <v-autocomplete
                    readonly
                    label="Extra Bed"
                    :items="[0, 1, 2, 3]"
                    dense
                    outlined
                    v-model="roomData.extra_bed_qty"
                    hide-details
                    required
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    readonly
                    rows="2"
                    v-model="BookingData.request"
                    label="Customer Request"
                    outlined
                    dense
                    hide-details
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-container>
          </v-col>
          <v-divider v-if="isGroupBooking" vertical></v-divider>
          <v-col :cols="isGroupBooking ? '4' : '6'">
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
              <v-col class="text-center">
                <v-container class="mt-2">
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
                    <v-col cols="12" class="pt-10">
                      <!-- <pre>{{ roomData }}</pre> -->

                      <table style="width: 100%">
                        <tr>
                          <td
                            class="text-left"
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            Room
                          </td>
                          <td
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
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
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            Paid
                          </td>
                          <td
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            {{
                              $utils.currency_format(BookingData.paid_amounts)
                            }}
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
                              $utils.currency_format(tempBalance)
                            }}</span>
                          </td>
                        </tr>
                      </table>
                    </v-col>
                  </v-row>
                </v-container>
              </v-col>
            </v-row>
          </v-col>
          <v-divider vertical></v-divider>
          <v-col :cols="isGroupBooking ? '4' : '6'">
            <v-row>
              <v-col>
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
                            {{ $utils.currency_format(item.balance) || "---" }}
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
                            {{ $utils.currency_format(totalTransactionAmount) }}
                          </td>
                        </tr>
                      </table>
                    </v-col>
                    <v-col cols="12">
                      <v-divider></v-divider>
                    </v-col>
                    <v-col cols="12">
                      <v-card outlined>
                        <v-container>
                          <v-row>
                            <v-col cols="12">
                              <Heading label="Enter Reason to cancel the room" />
                            </v-col>
                            <v-col cols="12">
                              <v-textarea
                                placeholder="Reason"
                                rows="4"
                                dense
                                outlined
                                v-model="reason"
                                hide-details
                              ></v-textarea>
                            </v-col>
                            <v-col cols="12" class="text-center mt-3">
                              <AssetsButtonCancel
                                @click="$emit(`close-dialog`)"
                              />
                              &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                              <AssetsButtonSubmit @click="store" />
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-container>
              </v-col>
            </v-row>
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
  props: ["BookingData", "roomData","evenIid"],

  data() {
    return {
      reason: null,
      dob_menu: false,
      guest: {},
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
        dob: null,
        exp_menu: false,
        exp: null,
      },
      after_discount_balance: 0,
      errors: [],

      checkInDialog: false,
      additional_charges: {},
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

      this.get_additional_charges();
    }
  },
  computed: {
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
    async get_additional_charges() {
      let { data } = await this.$axios.get(`additional_charges`, {
        params: {
          company_id: this.$auth.user.company_id,
        },
      });

      this.additional_charges = data;
    },
    set_additional_charges() {
      this.guest.bed_amount = this.guest.extra_bed_qty
        ? this.guest.extra_bed_qty * this.additional_charges.extra_bed
        : 0;
    },
    setNewBalance(tempBalance, discount) {
      this.full_payment = parseFloat(tempBalance) - parseFloat(discount);
    },
    get_after_discount_balance(amt = 0) {
      let discount = amt || 0;
      let blc = parseFloat(this.grand_remaining_price) - parseFloat(discount);
      this.after_discount_balance = blc.toFixed(2) || 0;
    },

    store() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }
      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id,
      };

      if (this.isGroupBooking) {
        payload.guest = this.guest;
      }

      // this.loading = true;
      this.$axios
        .post(`cancel_room/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;

            this.$swal(
              "Success!",
              "Room has been cancelled in",
              "success"
            ).then(() => {
              this.$emit("close-dialog");
            });
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
  },
};
</script>
