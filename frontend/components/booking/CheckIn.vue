<template>
  <v-dialog
    v-model="checkInDialog"
    persistent
    :max-width="isGroupBooking ? '1000' : '1000'"
  >
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> Check In </span>
    </template>
    <AssetsIconClose :left="990" @click="checkInDialog = false" />
    <div class="grey lighten-3 pa-2" style="overflow: hidden">
      <v-row>
        <v-col cols="4">
          <v-card style="border: 3px solid white; min-height: 477px">
            <v-card-text>
              <v-row no-gutter v-if="BookingData && BookingData.id">
                <v-col cols="12" class="pa-0 ma-0">
                  <v-row no-gutter>
                    <v-col cols="12">
                      <AssetsHeadDialog>
                        <template #label>
                          <small>Group Booking</small>
                        </template>
                      </AssetsHeadDialog>
                      <div class="mx-2">
                        <table cellspacing="0" style="width: 100%">
                          <tr>
                            <td class="blue--text">
                              <span> Payer </span>
                            </td>
                            <td class="text-right">
                              <v-btn
                                v-if="customerScreen"
                                @click="showRelatedScreen(`payment`)"
                                text
                                small
                                class="grey lighten-3"
                                ><v-icon color="primary">mdi-cash</v-icon>
                                Pay</v-btn
                              >

                              <v-btn
                                v-if="paymentScreen"
                                @click="showRelatedScreen(`guest`)"
                                text
                                small
                                class="grey lighten-3"
                                ><v-icon color="primary">mdi-account</v-icon>
                                Guest</v-btn
                              >
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <b
                                >{{ roomData.customer?.title || "---" }} :
                                {{ roomData.customer?.full_name || "---" }}</b
                              >
                            </td>
                          </tr>
                          <tr>
                            <td>
                              Tel : {{ roomData.customer?.contact_no || "---" }}
                            </td>
                          </tr>
                          <tr>
                            <td>
                              Email : {{ roomData.customer?.email || "---" }}
                            </td>
                          </tr>
                          <tr>
                            <td>Address : {{ customer_full_address }}</td>
                          </tr>
                        </table>
                        <table class="my-2" style="width: 100%">
                          <tr>
                            <td class="blue--text">
                              <span> Room Details </span>
                            </td>
                            <td class="red--text text-right">
                              <span>
                                Reservation # {{ BookingData.reservation_no }}
                              </span>
                            </td>
                          </tr>
                        </table>
                        <table style="width: 100%">
                          <tr>
                            <td width="50%" class="border-bottom">
                              Booking Price
                            </td>
                            <td width="50%" class="border-bottom">
                              <span class="blue--text">{{
                                $utils.currency_format(BookingData.total_price)
                              }}</span>
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Rooms</td>
                            <td width="50%" class="border-bottom">
                              {{ BookingData.booked_rooms.length }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Check In</td>
                            <td width="50%" class="border-bottom">
                              {{ roomData.checkin_datetime_only }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Check Out</td>
                            <td width="50%" class="border-bottom">
                              {{ roomData.checkout_datetime_only }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Extra Bed</td>
                            <td width="50%" class="border-bottom">
                              {{ roomData.extra_bed_qty }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Food</td>
                            <td width="50%" class="border-bottom">
                              {{ roomData.food_plan || "No Food" }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">
                              Early Check In
                            </td>
                            <td width="50%" class="border-bottom">
                              {{ roomData?.early_check_in ? "yes" : "no" }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">
                              Late Check Out
                            </td>
                            <td width="50%" class="border-bottom">
                              {{ roomData?.late_check_out ? "yes" : "no" }}
                            </td>
                          </tr>
                        </table>
                        <div class="mt-3">
                          Notes : {{ BookingData.request }}
                        </div>
                      </div>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="8">
          <v-card style="border: 3px solid white; min-height: 477px">
            <v-card-text>
              <v-row no-gutter v-if="BookingData && BookingData.id">
                <v-col cols="12" class="pa-0 ma-0">
                  <v-row v-if="customerScreen" no-gutter>
                    <v-col cols="12">
                      <AssetsHeadDialog>
                        <template #label>
                          <small>Guest Details</small>
                        </template>
                        <template #search>
                          <small
                            ><SearchCustomer
                              @foundCustomer="handleFoundCustomer"
                          /></small>
                        </template>
                      </AssetsHeadDialog>
                      <v-row no-gutters>
                        <v-col md="2" cols="12"></v-col>
                        <v-col>
                          <v-row>
                            <v-col class="my-1 mx-3" cols="3">
                              <v-text-field
                                readonly
                                dense
                                hide-details
                                outlined
                                label="Room Number"
                                v-model="BookingData.room_no"
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>

                      <BookingGroupCustomerInfo
                        @selectedCustomer="handleSelectedCustomer"
                        :defaultCustomer="defaultCustomer"
                        :key="customerCompKey"
                      />
                      <v-row>
                        <v-col md="12">
                          <v-textarea
                            class="custom-text-field"
                            rows="3"
                            label="Customer Request"
                            hide-details
                            outlined
                          ></v-textarea>
                        </v-col>
                      </v-row>
                    </v-col>
                    <v-col
                      cols="12"
                      class="text-center"
                      style="position: absolute; bottom: 0"
                    >
                      <AssetsButton
                        :options="{
                          color: `red`,
                          label: `cancel`,
                        }"
                        @click="$emit(`close-dialog`)"
                      />
                      <AssetsButton
                        :options="{
                          color: `green`,
                          label: `Submit`,
                        }"
                        @click="store"
                      />
                    </v-col>
                  </v-row>

                  <v-row v-if="paymentScreen" no-gutters>
                    <v-col cols="12">
                      <AssetsHeadDialog>
                        <template #label>
                          <small>Payment Details</small>
                        </template>
                      </AssetsHeadDialog>
                    </v-col>
                    <v-col cols="12">
                      <AssetsTable :headers="headers" :items="transactions">
                        <template #debit="{ item }">
                          {{ $utils.currency_format(item.debit) }}
                        </template>
                        <template #credit="{ item }">
                          {{ $utils.currency_format(item.credit) }}
                        </template>
                        <template #balance="{ item }">
                          {{ $utils.currency_format(item.balance) }}
                        </template>

                        <template #row>
                          <tr>
                            <td
                              colspan="4"
                              class="text-right primary--text pr-2"
                            >
                              Total Balance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              {{
                                $utils.currency_format(totalTransactionAmount)
                              }}
                            </td>
                          </tr>
                        </template>
                      </AssetsTable>
                    </v-col>
                    <v-col cols="12">
                      <v-row class="mt-4">
                        <v-col cols="6">
                          <v-card outlined class="pa-2">
                            <Heading label="Payment" />
                            <v-container>
                              <v-row>
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
                                <v-col cols="4">
                                  <v-text-field
                                    v-model="tempBalance"
                                    label="Balance"
                                    outlined
                                    dense
                                    hide-details
                                    @keyup="
                                      setNewBalance(tempBalance, discount)
                                    "
                                  ></v-text-field>
                                </v-col>
                                <v-col cols="4">
                                  <v-text-field
                                    v-model="discount"
                                    label="Discount"
                                    outlined
                                    dense
                                    hide-details
                                    @keyup="
                                      setNewBalance(tempBalance, discount)
                                    "
                                  ></v-text-field>
                                </v-col>
                                <v-col cols="4">
                                  <v-text-field
                                    v-model="after_discount_balance"
                                    label="After Discount"
                                    outlined
                                    dense
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                                <v-col>
                                  <v-text-field
                                    v-model="full_payment"
                                    label="Amount to Pay"
                                    outlined
                                    dense
                                    hide-details
                                  ></v-text-field>
                                </v-col>

                                <v-col cols="12" class="text-center">
                                  <AssetsButton
                                    :options="{
                                      color: `red`,
                                      label: `cancel`,
                                    }"
                                    @click="$emit(`close-dialog`)"
                                  />
                                  <AssetsButton
                                    :options="{
                                      color: `green`,
                                      label: `Submit`,
                                    }"
                                    @click="store"
                                  />
                                </v-col>
                              </v-row>
                            </v-container>
                          </v-card>
                        </v-col>

                        <v-col cols="6">
                          <v-card outlined class="pa-2">
                            <Heading label="Rooms" />
                            <v-container v-if="BookingData.booked_rooms.length">
                              <v-row no-gutters>
                                <v-col
                                  cols="3"
                                  v-for="(
                                    room, index
                                  ) in BookingData.booked_rooms"
                                  :key="index"
                                  dark
                                >
                                  <div
                                    :class="
                                      getRelatedClass(room.booking_status)
                                    "
                                    style="border-radius: 5px"
                                    class="text-center white--text pa-2 ma-1"
                                  >
                                    {{ room.room_no }}
                                  </div>
                                </v-col>
                              </v-row>
                            </v-container>
                          </v-card>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
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
      customerScreen: true,
      paymentScreen: false,
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
      defaultCustomer: {
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
      customerCompKey: 1,
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
        dob: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        exp_menu: false,
        exp: null,
      },
      after_discount_balance: 0,
      errors: [],

      checkInDialog: false,
      additional_charges: {},

      headers: [
        {
          text: `Date`,
          value: `created_at`,
          align: `left`,
          width: "250px",
        },
        { text: `Debit`, value: `debit`, align: `right` },
        { text: `Credit`, value: `credit`, align: `right` },
        { text: `Balance`, value: `balance`, align: `right` },
      ],
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

      this.actualCheckoutTime = this.roomData.check_out_time;

      this.calculateHoursQty(this.actualCheckoutTime);
      this.get_transaction();

      this.get_additional_charges();

      if (!this.isGroupBooking) {
        this.roomData.customer;
        this.customerCompKey += 1;
        this.defaultCustomer = this.roomData.customer;
        this.customer = this.roomData.customer;
      }
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
    getRelatedClass(status_id) {
      let status = {
        0: "available",
        1: "booked",
        2: "occupied",
        3: "checked_out",
        4: "blocked",
      };

      return status[status_id || ""];
    },
    showRelatedScreen(type = "guest") {
      // Define booleans based on the type
      const isPayment = type === "payment";

      // Assign values based on the condition
      this.paymentScreen = isPayment;
      this.customerScreen = !isPayment;
    },
    handleFoundCustomer(e) {
      this.customerCompKey += 1;

      if (this.isGroupBooking) {
        this.defaultCustomer = e;
        this.guest = e;
        return;
      }

      this.defaultCustomer = e;
      this.customer = e;
    },
    handleSelectedCustomer({ customer }) {
      if (this.isGroupBooking) {
        this.guest = customer;
        return;
      }
      this.customer = customer;
    },
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
      this.after_discount_balance =
        parseFloat(tempBalance) - parseFloat(discount);
    },

    store() {
      if (!this.defaultCustomer.title) {
        this.$swal("Warning", "Customer title is required", "error");
        return;
      }

      if (!this.defaultCustomer.first_name) {
        this.$swal("Warning", "Customer first name is required", "error");
        return;
      }

      if (!this.defaultCustomer.last_name) {
        this.$swal("Warning", "Customer last name is required", "error");
        return;
      }

      if (!this.defaultCustomer.contact_no) {
        this.$swal("Warning", "Customer Contact no is required", "error");
        return;
      }

      if (!this.defaultCustomer.nationality) {
        this.$swal("Warning", "Customer Nationality is required", "error");
        return;
      }
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

      if (this.isGroupBooking) {
        payload.guest = this.guest;
      }

      // this.loading = true;
      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;

            this.$swal("Success!", "Room has been checked in", "success").then(
              () => {
                this.$emit("close-dialog");
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
  },
};
</script>
