<template>
  <v-row no-gutter v-if="BookingData && BookingData.id">
    <v-col cols="6" class="text-center">
      <v-container>
        <v-row>
          <v-col cols="12">
            <div class="text-right">
              <v-icon
                color="primary"
                @click="$router.push(`customer/details/${BookingData.id}`)"
                >mdi-eye</v-icon
              >
            </div>
            <!-- <pre>{{ roomData }}</pre> -->
            <v-avatar size="125" class="">
              <v-img
                :src="
                  (roomData?.customer?.captured_photo) ||
                  '/no-profile-image.jpg'
                "
              ></v-img>
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
          <v-col cols="12">
            <v-textarea
              rows="2"
              v-model="customer_full_address"
              readonly
              label="Address"
              outlined
              dense
              hide-details
            ></v-textarea>
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
          <v-col cols="6">
            <v-text-field
              v-model="BookingData.total_price"
              readonly
              label="Total Amount"
              outlined
              dense
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="BookingData.paid_amounts"
              readonly
              label="Paid"
              outlined
              dense
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="remaining_price"
              readonly
              label="Balance To Pay"
              outlined
              dense
              hide-details
            ></v-text-field>
          </v-col>
        </v-row>
      </v-container>
    </v-col>
    <!-- Data table on the right -->
    <v-col cols="6">
      <v-container>
        <v-row>
          <v-col cols="12">
            <div style="overflow: auto; min-height: 370px; max-height: 300px">
              <table style="width: 100%">
                <tr style="font-size: 13px">
                  <td class="text-center">Date</td>
                  <td class="text-center">Debit</td>
                  <td class="text-center">Credit</td>
                  <td class="text-center">Balance</td>
                  <td class="text-center">Receipt</td>
                </tr>

                <tr
                  v-for="(item, index) in transactions"
                  :key="index"
                  style="font-size: 13px"
                >
                  <td class="text-center">{{ item.created_at || "---" }}</td>
                  <td class="text-center">
                    {{ item && item.debit == 0 ? "---" : item.debit }}
                  </td>
                  <td class="text-center">
                    {{ item && item.credit == 0 ? "---" : item.credit }}
                  </td>
                  <td class="text-center">{{ item.balance || "---" }}</td>
                  <td class="text-center">
                    {{ item.id }}
                  </td>
                </tr>
                <!-- <tr style="font-size: 13px; background-color: white; color: black">
            <th colspan="4" class="text-center">Balance</th>
            <td class="text-right" style="background-color: white">
              {{ totalTransactionAmount + exceedHoursCharges }}
            </td>
          </tr> -->
              </table>
            </div>
          </v-col>
          <v-col cols="12" class="align-self-end">
            <v-autocomplete
              label="Payment Mode"
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
          <v-col cols="12" v-if="payment_mode_id != 1">
            <v-text-field
              label="Reference"
              dense
              outlined
              type="text"
              v-model="reference"
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="full_payment"
              label="Amount"
              outlined
              dense
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-checkbox
              dense
              label="Print Invoice"
              v-model="isPrintInvoice"
              hide-details
            ></v-checkbox>
          </v-col>
          <v-col cols="12">
            <v-btn color="primary" block @click="store_check_out">Pay</v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-col>
  </v-row>
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
