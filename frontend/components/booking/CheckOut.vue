<template>
  <div>
    <v-row>
      <v-col md="7">
        <v-container>
          <table>
            <v-progress-linear
              v-if="false"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
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
                {{ BookingData && BookingData.check_in_date }}
              </td>
            </tr>
            <tr>
              <th>Check Out</th>
              <td>
                {{ BookingData && BookingData.check_out_date }}
              </td>
            </tr>
            <tr>
              <th>
                Payment Mode
                <span class="text-danger">*</span>
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
                    { id: 7, name: 'City Ledger' },
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
                <span class="text-danger">*</span>
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
              <td>{{ BookingData.remaining_price }}</td>
            </tr>
            <tr>
              <th>Remaining Balance With Posting</th>
              <td>{{ BookingData.grand_remaining_price }}</td>
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
                <span class="text-danger">*</span>
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
          </table>
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
              {{ totalTransactionAmount }}
            </td>
          </tr>
        </table>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  props: ["BookingData"],
  data() {
    return {
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

  created() {
    this.preloader = false;
  },

  mounted() {
    this.checkOutDialog = true;
    this.after_discount_balance = this.BookingData.grand_remaining_price;
    this.get_transaction();
  },

  computed: {},
  methods: {
    get_after_discount_balance(amt = 0) {
      let discount = amt || 0;
      console.log(discount);
      let blc =
        parseFloat(this.BookingData.grand_remaining_price) -
        parseFloat(discount);
      this.after_discount_balance = blc.toFixed(2) || 0;
    },

    get_transaction() {
      let id = this.BookingData.id;
      console.log(id);
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

    store_check_out() {
      this.loading = true;
      let payload = {
        booking_id: this.BookingData.id,
        grand_remaining_price: this.BookingData.grand_remaining_price,
        remaining_price: this.BookingData.remaining_price,
        full_payment: this.full_payment,
        payment_mode_id: this.BookingData.payment_mode_id,
        company_id: this.$auth.user.company.id,
        isPrintInvoice: this.isPrintInvoice,
        reference_number: this.reference,
        discount: this.discount,
      };

      this.$axios
        .post("/check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;
            this.closeDialog(data);
            this.alert("Success", "Successfully Checkout", "success");
            if (this.isPrintInvoice) {
              this.redirect_to_invoice(data.bookingId);
            }
          }
        })
        .catch((e) => console.log(e));
    },

    closeDialog(data) {
      this.$emit("close-dialog", data);
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

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>

<style scoped src="@/assets/custom/checkout.css"></style>
