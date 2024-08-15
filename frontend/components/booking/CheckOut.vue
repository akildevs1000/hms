<template>
  <div>
    <v-row v-if="BookingData && BookingData.id">
      <v-col md="7">
        <v-container>
          <table>
            <tr>
              <th>Customer Name</th>
              <td style="width: 300px">
                {{ BookingData && BookingData.title }}
              </td>
            </tr>
            <tr>
              <th>Room No</th>
              <td>
                {{ BookingData.room_no }}
              </td>
            </tr>
            <tr>
              <th>Room Type</th>
              <td>
                {{ BookingData.room_type }}
              </td>
            </tr>
            <tr>
              <th>Check In</th>
              <td>
                {{ roomData && roomData.check_in }}
              </td>
            </tr>
            <tr>
              <th>Check Out</th>
              <td>
                {{ roomData && roomData.check_out }}
              </td>
            </tr>
            <tr>
              <th>Total Booking Hours</th>
              <td>
                {{ roomData && roomData.total_booking_hours }}
              </td>
            </tr>
            <tr>
              <th>
                Payment Mode
                <span class="error--text">*</span>
              </th>
              <td>
                <v-select
                  v-model="BookingData.payment_mode_id"
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
            <tr v-if="BookingData.payment_mode_id != 1">
              <th>
                Reference Number
                <span class="error--text">*</span>
              </th>
              <td>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="reference"
                  :hide-details="true"
                ></v-text-field>
              </td>
            </tr>
            <tr>
              <th>Total Amount</th>
              <td>{{ BookingData && BookingData.total_price }}</td>
            </tr>
            <tr>
              <th>Total Posting Amount</th>
              <td>
                {{ BookingData && BookingData.total_posting_amount }}
              </td>
            </tr>
            <tr>
              <th>Remaining Balance</th>
              <td>{{ remaining_price }}</td>
            </tr>
            <tr>
              <th>Remaining Balance With Posting</th>
              <td>{{ grand_remaining_price }}</td>
            </tr>
            <tr style="background-color: white" v-if="BookingData.paid_by != 2">
              <th>Discount</th>
              <td>
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="discount"
                  :hide-details="true"
                  @keyup="get_after_discount_balance(discount)"
                ></v-text-field>
              </td>
            </tr>
            <tr style="background-color: white" v-if="BookingData.paid_by != 2">
              <th>After Discount Balance</th>
              <td>
                {{ after_discount_balance }}
              </td>
            </tr>
            <tr style="background-color: white" v-if="BookingData.paid_by != 2">
              <th>
                Full Payment
                <span class="error--text">*</span>
              </th>
              <td>
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="full_payment"
                  :hide-details="true"
                ></v-text-field>
              </td>
            </tr>
            <tr>
              <th>Print Invoice</th>
              <td>
                <v-checkbox
                  v-model="isPrintInvoice"
                  :hide-details="true"
                  class="pt-0 py-1 chk-align"
                >
                </v-checkbox>
              </td>
            </tr>
            <tr>
              <th>Change CheckOut Time</th>
              <td>
                <v-menu
                  ref="menu"
                  v-model="change_checkout_time"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="actualCheckoutTime"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      outlined
                      dense
                      v-model="actualCheckoutTime"
                      label=""
                      append-icon="mdi-clock-time-four-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      hide-details
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    v-if="change_checkout_time"
                    v-model="actualCheckoutTime"
                    no-title
                    @click:minute="
                      () => {
                        $refs.menu.save(actualCheckoutTime);
                        calculateHoursQty(actualCheckoutTime);
                      }
                    "
                    format="24hr"
                  ></v-time-picker>
                </v-menu>
              </td>
            </tr>
            <tr>
              <th>Additional Hours</th>
              <td>
                {{ exceedHoursCharges }}
              </td>
            </tr>
          </table>
          <!-- <v-text-field
            readonly
            class="my-2"
            v-if="Testing"
            label="Assumed Time"
            dense
            outlined
            hide-details
            v-model="actualCheckoutTime"
          ></v-text-field> -->
          <v-btn
            class="primary mt-3"
            height="40"
            width="25%"
            small
            :loading="loading"
            @click="store_check_out"
          >
            Check Out
          </v-btn>
        </v-container>
      </v-col>
      <v-col md="5" class="mt-3">
        <table>
          <tr style="font-size: 13px; background-color: white; color: black">
            <th>#</th>
            <th>Date</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
          </tr>

          <tr
            v-for="(item, index) in transactions"
            :key="index"
            style="font-size: 13px; background-color: white; color: black"
          >
            <td>
              <b>{{ ++index }}</b>
            </td>
            <td>{{ item.created_at || "---" }}</td>
            <td class="text-right">
              {{ item && item.debit == 0 ? "---" : item.debit }}
            </td>
            <td class="text-right">
              {{ item && item.credit == 0 ? "---" : item.credit }}
            </td>
            <td class="text-right">{{ item.balance || "---" }}</td>
          </tr>
          <tr style="font-size: 13px; background-color: white; color: black">
            <th colspan="4" class="text-right">Balance</th>
            <td class="text-right" style="background-color: white">
              {{ totalTransactionAmount + exceedHoursCharges }}
            </td>
          </tr>
        </table>
      </v-col>
    </v-row>
  </div>
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
  mounted() {
    this.checkOutDialog = true;
  },
  created() {
    this.preloader = false;
    if (this.roomData && this.roomData.id) {
      let { grand_remaining_price, remaining_price } = this.BookingData;
      this.grand_remaining_price = grand_remaining_price;
      this.remaining_price = remaining_price;
      this.after_discount_balance = grand_remaining_price;

      this.actualCheckoutTime = this.roomData.check_out_time;

      this.calculateHoursQty(this.actualCheckoutTime);
      this.get_transaction();
    }
  },
  computed: {},
  methods: {
    get_after_discount_balance(amt = 0) {
      let discount = amt || 0;
      let blc = parseFloat(this.grand_remaining_price) - parseFloat(discount);
      this.after_discount_balance = blc.toFixed(2) || 0;
    },

    store_check_out() {
      let full_payment = parseFloat(this.full_payment);
      if (full_payment <= 0) {
        this.alert("Warning", "Payment should be greater than zero","error");
        return;
      }
      let payload = {
        booking_id: this.BookingData.id,
        grand_remaining_price: this.grand_remaining_price,
        remaining_price: this.remaining_price,
        full_payment: parseFloat(this.full_payment),
        payment_mode_id: this.BookingData.payment_mode_id,
        company_id: this.$auth.user.company.id,
        isPrintInvoice: this.isPrintInvoice,
        reference_number: this.reference,
        discount: this.discount,
        user_id: this.$auth.user.id,
        isHall: this.isHall,
        exceedHoursCharges: this.exceedHoursCharges,
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

            this.closeDialog(payload);
            this.alert("Success", "Successfully Checkout", "success");
            if (this.isPrintInvoice) {
              this.redirect_to_invoice(data.bookingId);
            }
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

<style scoped src="@/assets/css/checkout.css"></style>
