<template>
  <div>
    <v-row>
      <v-col md="7">
        <v-container>
          <table>
            <v-progress-linear v-if="false" :active="loading" :indeterminate="loading" absolute
              color="primary"></v-progress-linear>
            <tr>
              <th>Guest Name</th>
              <td style="width: 300px">
                {{ BookingData && BookingData.customer.title }}

                {{ BookingData.customer.first_name }} {{ BookingData.customer.last_name }}
              </td>
            </tr>
            <tr>
              <th>Room No</th>
              <td>
                {{ BookingData.room_number }}
              </td>
            </tr>
            <tr>
              <th>Date</th>
              <td>
                {{ BookingData && BookingData.event_date }}
              </td>
            </tr>

            <tr>
              <th>
                Pay Mode
                <span class="error--text">*</span>
              </th>
              <td>
                <v-select v-model="BookingData.payment_mode_id" :items="[
                  { id: 1, name: 'Cash' },
                  { id: 2, name: 'Card' },
                  { id: 3, name: 'Online' },
                  { id: 4, name: 'Bank' },
                  { id: 5, name: 'UPI' },
                  { id: 6, name: 'Cheque' },
                ]" item-text="name" item-value="id" dense outlined :hide-details="true" :height="1"></v-select>
              </td>
            </tr>
            <tr v-if="BookingData.payment_mode_id != 1">
              <th>
                Reference Number
                <span class="error--text">*</span>
              </th>
              <td>
                <v-text-field dense outlined type="text" v-model="reference" :hide-details="true"></v-text-field>
              </td>
            </tr>
            <tr v-if="can()">
              <th>
                Description
                <span class="error--text">*</span>
              </th>
              <td>
                <v-text-field dense outlined type="text" v-model="desc" :hide-details="true"></v-text-field>
              </td>
            </tr>
            <tr>
              <th>Total Amount</th>
              <td>{{ BookingData && BookingData.total_price }}</td>
            </tr>

            <tr>
              <th>Balance Due</th>
              <td>{{ BookingData.balance }}</td>
            </tr>

            <tr style="background-color: white">
              <th>
                Payment
                <span class="error--text">*</span>
              </th>
              <td>
                <v-text-field dense outlined type="number" v-model="new_payment" :hide-details="true"></v-text-field>
              </td>
            </tr>
            <tr></tr>
          </table>
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

          <tr v-for="(item, index) in transactions" :key="index"
            style="font-size: 13px; background-color: white; color: black">
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
    <v-btn class="primary mt-3" small @click="store_advance(BookingData)" :loading="loading">
      Pay
    </v-btn>
  </div>
</template>
<script>
export default {
  props: ["BookingData", "msg"],
  data() {
    return {
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      new_payment: 0,
      reference: "",
      desc: "",
      totalTransactionAmount: 0,
      transactions: [],
      errors: [],
      checkOutDialog: false,
    };
  },

  watch: {
    BookingData() {
      this.get_transaction();
    },
  },

  created() {
    this.preloader = false;
  },

  mounted() {
    this.get_transaction();
  },

  computed: {},

  methods: {
    get_transaction() {
      let id = this.BookingData.id;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          room_type: 'Hall',
        },
      };
      this.$axios
        .get(`get_transaction_by_booking_id/${id}`, payload)
        .then(({ data }) => {
          this.transactions = data.transactions;
          this.totalTransactionAmount = data.totalTransactionAmount;
        });
    },

    store_advance(data) {
      if (this.new_payment == "") {
        alert("Enter payment amount");
        return;
      }

      if (!this.can() && this.new_payment < 0) {
        alert("The number cannot be negative.");
        return;
      }

      if (this.can()) {
        console.log("1");
        if (this.desc == "") {
          console.log("2");
          alert("Enter description");
          return;
        }
      }
      this.loading = true;
      let payload = {
        new_advance: this.new_payment,
        reference_number: this.reference,
        desc: this.desc || this.msg || "advance payment",
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
        company_id: this.$auth.user.company.id,
        user_id: this.$auth.user.id,
      };
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.new_payment = 0;
            this.get_transaction();
            this.closeDialog(data);
            this.loading = false;
          }
        })
        .catch((e) => console.log(e));
    },

    closeDialog(data) {
      this.$emit("close-dialog", data);
    },

    can() {
      return this.$auth.user.user_type == "company" ? true : false;
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

<style scoped src="@/assets/css/checkout.css"></style>
