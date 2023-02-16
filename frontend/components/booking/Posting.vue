<template>
  <div>
    <v-card-text>
      <v-container>
        <br />
        <table>
          <v-progress-linear
            v-if="false"
            :active="loading"
            :indeterminate="loading"
            absolute
            color="primary"
          ></v-progress-linear>
          <tr>
            <th>Bill No</th>
            <td style="width: 300px">
              <v-text-field
                dense
                outlined
                type="number"
                v-model="posting.bill_no"
                :hide-details="true"
              ></v-text-field>
            </td>
          </tr>
          <tr>
            <th>Customer Name</th>
            <td style="width: 300px">
              {{ checkData && checkData.title }}
            </td>
          </tr>
          <tr>
            <th>Room No</th>
            <td>
              {{ checkData.room_no }}
            </td>
          </tr>
          <tr>
            <th>Room Type</th>
            <td>
              {{ checkData.room_type }}
            </td>
          </tr>
          <tr style="background-color: white">
            <th>
              Item
              <span class="text-danger">*</span>
            </th>
            <td>
              <v-text-field
                dense
                outlined
                type="text"
                v-model="posting.item"
                :hide-details="true"
              ></v-text-field>
            </td>
          </tr>
          <tr style="background-color: white">
            <th>
              QTY
              <span class="text-danger">*</span>
            </th>
            <td>
              <v-text-field
                dense
                outlined
                type="number"
                v-model="posting.qty"
                :hide-details="true"
              ></v-text-field>
            </td>
          </tr>
          <tr style="background-color: white">
            <th>
              Amount
              <span class="text-danger">*</span>
            </th>
            <td>
              <v-text-field
                dense
                outlined
                type="number"
                v-model="posting.amount"
                :hide-details="true"
                @keyup="get_amount_with_tax(posting.tax_type)"
              ></v-text-field>
            </td>
          </tr>
          <tr style="background-color: white">
            <th>
              Type
              <span class="text-danger">*</span>
            </th>
            <td>
              <v-select
                v-model="posting.tax_type"
                :items="[
                  { id: -1, name: 'select..' },
                  { name: 'Food' },
                  { name: 'Others' },
                  { name: 'Mesentery' },
                  { name: 'ExtraBed' },
                ]"
                item-text="name"
                item-value="id"
                dense
                outlined
                :hide-details="true"
                :height="1"
                @change="get_amount_with_tax(posting.tax_type)"
              ></v-select>
            </td>
          </tr>
          <tr style="background-color: white">
            <th>
              Amount With Tax
              <span class="text-danger">*</span>
            </th>
            <td>
              {{ posting.amount_with_tax }}
            </td>
          </tr>
          <tr></tr>
        </table>
      </v-container>
    </v-card-text>
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
