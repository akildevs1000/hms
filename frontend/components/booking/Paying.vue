<template>
  <v-row>
    <v-col>
      <table cellspacing="0" style="width: 100%">
        <tr>
          <td width="50%" class="border-bottom">Guest Name</td>
          <td width="50%" class="border-bottom">
            {{ BookingData && BookingData.title }}
          </td>
        </tr>
        <tr>
          <td width="50%" class="border-bottom">Room No</td>
          <td width="50%" class="border-bottom">
            {{ bookedRooms }}
          </td>
        </tr>
        <tr>
          <td width="50%" class="border-bottom">Check In</td>
          <td width="50%" class="border-bottom">
            {{ BookingData && BookingData.check_in_date }}
          </td>
        </tr>
        <tr>
          <td width="50%" class="border-bottom">Check Out</td>
          <td width="50%" class="border-bottom">
            {{ BookingData && BookingData.check_out_date }}
          </td>
        </tr>
        <tr>
          <td width="50%" class="border-bottom">
            Pay Mode
            <span class="error--text">*</span>
          </td>
          <td width="50%" class="border-bottom py-1">
            <v-autocomplete
              outlined
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
              hide-details
            ></v-autocomplete>
          </td>
        </tr>
        <tr v-if="BookingData.payment_mode_id != 1">
          <td width="50%" class="border-bottom">
            Reference Number
            <span class="error--text">*</span>
          </td>
          <td width="50%" class="border-bottom">
            <v-text-field
              outlined
              dense
              type="text"
              v-model="reference"
              hide-details
            ></v-text-field>
          </td>
        </tr>
        <tr v-if="can()">
          <td width="50%" class="border-bottom">
            Description
            <span class="error--text">*</span>
          </td>
          <td width="50%" class="border-bottom py-1">
            <v-text-field
              dense
              outlined
              type="text"
              v-model="desc"
              hide-details
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td width="50%" class="border-bottom">Total Amount</td>
          <td width="50%" class="border-bottom">
            {{ $utils.currency_format(BookingData && BookingData.total_price) }}
          </td>
        </tr>

        <tr>
          <td width="50%" class="border-bottom">Balance Due</td>
          <td width="50%" class="border-bottom">
            {{ $utils.currency_format(BookingData.balance) }}
          </td>
        </tr>

        <tr style="background-color: white">
          <td width="50%" class="border-bottom">
            Payment
            <span class="error--text">*</span>
          </td>
          <td width="50%" class="border-bottom py-1">
            <v-text-field
              dense
              outlined
              type="number"
              v-model="new_payment"
              hide-details
            ></v-text-field>
          </td>
        </tr>
        <tr></tr>
      </table>
    </v-col>
    <v-col>
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
            <td colspan="4" class="text-right primary--text pr-2">
              Total Balance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{ $utils.currency_format(totalTransactionAmount) }}
            </td>
          </tr>
        </template>
      </AssetsTable>
    </v-col>
    <v-col cols="12" class="text-center">
      <AssetsButton
        :options="{
          label: `Cancel`,
          color: `red`,
        }"
        @click="store_advance(BookingData)"
      />
      &nbsp;
      <AssetsButton
        :options="{
          label: `Submit`,
          color: `green`,
        }"
        @click="store_advance(BookingData)"
      />
    </v-col>
  </v-row>
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
      this.get_transaction();
    },
  },

  created() {
    this.preloader = false;
  },

  mounted() {
    this.get_transaction();
  },

  computed: {
    bookedRooms() {
      return (
        this.BookingData &&
        this.BookingData.booked_rooms.map((e) => e.room_no).join(", ")
      );
    },
  },

  methods: {
    get_transaction() {
      let id = this.BookingData.id;
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
