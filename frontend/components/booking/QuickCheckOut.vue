<template>
  <v-dialog v-model="checkOutDialog" persistent max-width="800px">
    <template v-slot:activator="{ on, attrs }">
      <div style="text-align: center">
        <v-btn
          dense
          x-small
          v-bind="attrs"
          v-on="on"
          class="text-center"
          title="Quick Checkout"
          color="#34444c"
          style="width: 37px; height: 26px"
        >
          <v-icon color="white" style="transform: rotateY(180deg)"
            >mdi-clock-fast</v-icon
          >
        </v-btn>
        <div style="font-size: 10px; text-align: center">Q.CO</div>
      </div>
    </template>
    <v-card>
      <v-toolbar class="rounded-md" color="primary" dense flat dark>
        <span>Quick Checkout</span>
        <v-spacer></v-spacer>
        <!-- <v-icon dark class="pa-0" @click="getGroupList"> mdi-sync </v-icon> -->
        <v-icon dark class="pa-0" @click="checkOutDialog = false">
          mdi-close
        </v-icon>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <!-- <pre>{{ BookingData }}</pre> -->
          <v-row>
            <v-col md="6" cols="12" sm="12">
              <v-row>
                <v-col cols="12" class="text-center">
                  <div class="text-right">
                    <v-icon
                      color="primary"
                      @click="
                        $router.push(`customer/details/${BookingData.id}`)
                      "
                      >mdi-eye</v-icon
                    >
                  </div>
                  <v-avatar size="125" class="">
                    <v-img
                      :src="
                        (customer && customer.captured_photo) ||
                        '/no-profile-image.jpg'
                      "
                    ></v-img>
                  </v-avatar>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    multiple
                    v-model="selectedRooms"
                    :items="rooms"
                    label="Rooms *"
                    dense
                    item-text="room_no"
                    item-value="room_id"
                    outlined
                    hide-details
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="customer.full_name"
                    readonly
                    label="Full Name"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="customer.contact_no"
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
                    v-model="BookingData.check_in"
                    readonly
                    label="Check IN"
                    outlined
                    dense
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="BookingData.check_out"
                    readonly
                    label="Check Out"
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
            </v-col>

            <v-col cols="6">
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <v-card
                      outlined
                      v-if="!transactions.length"
                      style="
                        min-height: 430px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                      "
                    >
                      No Data Found
                    </v-card>

                    <div
                      v-else
                      style="
                        overflow: auto;
                        min-height: 430px;
                        max-height: 430px;
                      "
                    >
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
                          <td class="text-center">
                            {{ item.created_at || "---" }}
                          </td>
                          <td class="text-center">
                            {{ item && item.debit == 0 ? "---" : item.debit }}
                          </td>
                          <td class="text-center">
                            {{ item && item.credit == 0 ? "---" : item.credit }}
                          </td>
                          <td class="text-center">
                            {{ item.balance || "---" }}
                          </td>
                          <td class="text-center">
                            {{ item.id }}
                          </td>
                        </tr>
                      </table>
                    </div>
                  </v-col>
                  <v-col cols="12">
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
                    <!-- <v-icon
                      color="primary"
                      left
                      @click="redirect_to_invoice(BookingData.id)"
                      >mdi-printer</v-icon
                    >Invoice -->
                    <v-checkbox
                      dense
                      label="Print Invoice"
                      v-model="isPrintInvoice"
                      hide-details
                    ></v-checkbox>
                  </v-col>
                  <!-- <v-col cols="6" class="text-right">
                    <v-icon color="primary" left>mdi-file</v-icon>Invoice
                  </v-col> -->

                  <v-col cols="12">
                    <v-btn color="primary" block @click="store_check_out"
                      >Pay</v-btn
                    >
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
        </v-container>
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
  data() {
    return {
      groupBookingId: 1,
      BookingData: {
        id: 1,
        checkin_datetime_only: null,
        checkout_datetime_only: null,
        total_price: 0,
        paid_amounts: 0,
      },
      groupList: [],
      rooms: [],
      selectedRooms: [],
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
        full_name: null,
        contact_no: null,
        captured_photo: null,
      },
      customer_full_address: null,
      errors: [],
      checkOutDialog: false,
    };
  },
  created() {
    this.preloader = false;
    this.getGroupList();

    // if (this.roomData && this.roomData.id) {
    //   let { grand_remaining_price, remaining_price } = this.BookingData;
    //   this.grand_remaining_price = grand_remaining_price;
    //   this.remaining_price = remaining_price;
    //   this.after_discount_balance = grand_remaining_price;

    //   this.actualCheckoutTime = this.roomData.check_out_time;

    //   this.calculateHoursQty(this.actualCheckoutTime);
    // }
  },

  methods: {
    getGroupList() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`group-list`, config).then(({ data }) => {
        this.groupList = data;
      });
    },

    async getBookedRoomList(booking) {
      if (!booking) {
        return;
      }
      let url = `get-checked-in-room-list?booking_id=${booking.id}`;
      let { data } = await this.$axios.get(url);

      this.rooms = data;

      this.selectedRooms = data.map((e) => e.room_id);

      let { customer, ...rest } = booking;

      let {
        state,
        city,
        zip_code,
        country,
        full_name,
        contact_no,
        captured_photo,
      } = customer;

      this.customer = {
        full_name,
        contact_no,
        captured_photo,
      };

      this.customer_full_address = `${state || "---"}, ${city || "---"}, ${
        zip_code || "---"
      }, ${country || "---"}`;

      this.BookingData = rest;

      this.get_transaction();

      this.remaining_price = rest.remaining_price;

      // this. = booking;
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
        selectedRooms: this.selectedRooms,
      };

      // this.loading = true;
      this.$axios
        .post("/quick_check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;
            this.$emit("success");

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