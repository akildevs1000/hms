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
        <!-- <v-btn
          class="primary"
          height="70"
          width="100%"
          small
          @click="store_check_out"
        >
          Check Out
        </v-btn> -->
      </v-col>
    </v-row>
  </div>
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";
export default {
  props: ["BookingData"],
  components: {
    History,
    ImagePreview,
  },
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
      // if (this.full_payment == "") {
      //   alert("enter correct payment");
      //   return true;
      // }
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
          } else {
            this.alert("Success", "Successfully Checkout", "success");
            if (this.isPrintInvoice) {
              this.redirect_to_invoice(data.bookingId);
            }
            location.reload();
          }
        })
        .catch((e) => console.log(e));
    },

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${process.env.BACKEND_URL}invoice/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    preview(file) {
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      // let title = upper.replace(/[^A-Z]/g, " ");
      return upper;
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

<style scoped>
.chart {
  display: flex;
  align-items: flex-end;
}

.bar {
  width: 20px;
  margin-right: 10px;
  background-color: blue;
  transition: height 0.5s;
}

.app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}

.portrait.v-card {
  margin: 0 auto;
  max-width: 600px;
  width: 100%;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #e9e9e9;
}
td,
th {
  text-align: left;
  padding: 8px;
  border: 1px solid #e9e9e9;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
.fc-license-message {
  display: none !important;
}
.bg-background {
  background-color: #34444c !important;
}

.bg-background th,
td {
  border-top: none !important;
  border-right: none !important;
  border-left: none !important;
}

.element {
  height: 60px;
  width: 100%;
  margin: 0 auto;
  background-color: lime;
  animation-name: stretch;
  animation-duration: 1s;
  animation-timing-function: ease-out;
  animation-direction: alternate;
  animation-iteration-count: infinite;
  animation-play-state: running;
}

@keyframes stretch {
  0% {
    transform: scale(0.8);
    background-color: orange;
  }

  50% {
    background-color: orange;
  }

  100% {
    transform: scale(1);
    background-color: orange;
  }
}
</style>
