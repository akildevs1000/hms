<template>
  <v-dialog
    v-model="checkInDialog"
    persistent
    :max-width="isGroupBooking ? '1100' : '850'"
    style="position: relative"
  >
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> Check In </span>
    </template>
    <span
      style="position: absolute; z-index: 1; left: 1250px"
      :style="closeIconPosition"
    >
      <v-icon
        @click="checkInDialog = false"
        color="white"
        class="grey"
        size="30"
        style="border-radius: 50px; width: 15px; height: 15px"
      >
        mdi-close-circle
      </v-icon>
    </span>
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
                          <span>Group Booking</span>
                        </template>
                      </AssetsHeadDialog>
                      <table cellspacing="0" style="width: 100%">
                        <tr>
                          <td class="blue--text">
                            <span> Payer </span>
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
                    </v-col>
                    <v-col cols="12">
                      <table style="width: 100%">
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
                      <v-container>
                        <table border="1" style="width: 100%">
                          <tr>
                            <td style="padding: 3px" width="50%">Inv</td>
                            <td style="padding: 3px" width="50%">1234</td>
                          </tr>
                          <tr>
                            <td style="padding: 3px" width="50%">Rooms</td>
                            <td style="padding: 3px" width="50%">
                              {{ BookingData.room_no }}
                            </td>
                          </tr>

                          <tr>
                            <td style="padding: 3px" width="50%">Check In</td>
                            <td style="padding: 3px" width="50%">
                              {{ roomData.checkin_datetime_only }}
                            </td>
                          </tr>

                          <tr>
                            <td style="padding: 3px" width="50%">Check Out</td>
                            <td style="padding: 3px" width="50%">
                              {{ roomData.checkout_datetime_only }}
                            </td>
                          </tr>

                          <tr>
                            <td style="padding: 3px" width="50%">Extra Bed</td>
                            <td style="padding: 3px" width="50%">
                              {{ roomData.extra_bed_qty }}
                            </td>
                          </tr>

                          <tr>
                            <td style="padding: 3px" width="50%">Food</td>
                            <td style="padding: 3px" width="50%">
                              {{ roomData.food_plan || "No Food" }}
                            </td>
                          </tr>

                          <tr>
                            <td style="padding: 3px" width="50%">
                              Early Check In
                            </td>
                            <td style="padding: 3px" width="50%">
                              {{ roomData?.early_check_in ? "yes" : "no" }}
                            </td>
                          </tr>

                          <tr>
                            <td style="padding: 3px" width="50%">
                              Late Check Out
                            </td>
                            <td style="padding: 3px" width="50%">
                              {{ roomData?.late_check_out ? "yes" : "no" }}
                            </td>
                          </tr>
                        </table>
                      </v-container>
                      <div>Notes</div>
                      <div>{{ BookingData.request }}</div>
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
                  <v-row no-gutter>
                    <v-col cols="12">
                      <AssetsHeadDialog>
                        <template #label>
                          <span>Group Booking</span>
                        </template>
                      </AssetsHeadDialog>
                      <BookingCustomerInfo  />
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

      this.actualCheckoutTime = this.roomData.check_out_time;

      this.calculateHoursQty(this.actualCheckoutTime);
      this.get_transaction();

      this.get_additional_charges();
    }
  },
  computed: {
    closeIconPosition() {
      const minHeight = {
        Company: "115px",
        default: "100px",
      };

      return `top:${minHeight[this.vendorObject?.type] || minHeight.default};`;
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
