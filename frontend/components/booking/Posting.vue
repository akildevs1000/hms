<template>
  <div>
    <table>
      <v-progress-linear v-if="false" :active="loading" :indeterminate="loading" absolute
        color="primary"></v-progress-linear>
      <tr>
        <th>Bill No</th>
        <td style="width: 300px">
          <v-text-field dense outlined type="number" v-model="posting.bill_no" :hide-details="true"></v-text-field>
        </td>
      </tr>
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
      <tr style="background-color: white">
        <th>
          Item
          <span class="text-danger">*</span>
        </th>
        <td>
          <v-text-field dense outlined type="text" v-model="posting.item" :hide-details="true"></v-text-field>
        </td>
      </tr>
      <tr style="background-color: white">
        <th>
          QTY
          <span class="text-danger">*</span>
        </th>
        <td>
          <v-text-field dense outlined type="number" v-model="posting.qty" :hide-details="true"></v-text-field>
        </td>
      </tr>
      <tr style="background-color: white">
        <th>
          Amount
          <span class="text-danger">*</span>
        </th>
        <td>
          <v-text-field dense outlined type="number" v-model="posting.single_amt"
            @keyup="get_multiple_amount(posting.single_amt)" :hide-details="true"></v-text-field>
        </td>
      </tr>
      <tr style="background-color: white">
        <th>Total Amount</th>
        <td>
          <v-text-field dense outlined type="number" readonly v-model="posting.amount" :hide-details="true"
            @keyup="get_amount_with_tax(posting.tax_type)"></v-text-field>
        </td>
      </tr>
      <tr style="background-color: white">
        <th>
          Type
          <span class="text-danger">*</span>
        </th>
        <td>
          <v-select v-model="posting.tax_type" :items="[
            { id: -1, name: 'select..' },
            { name: 'Food' },
            { name: 'Misc' },
            { name: 'ExtraBed' },
            { name: 'Others' },
          ]" item-text="name" item-value="id" dense outlined :hide-details="true" :height="1"
            @change="get_amount_with_tax(posting.tax_type)"></v-select>
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

    <v-btn class="primary mt-3" :loading="loading" small @click="store_posting">Post</v-btn>
  </div>
</template>
<script>
export default {
  props: ["BookingData", "evenIid"],
  data() {
    return {
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,

      posting: {
        item: "",
        qty: "",
        amount: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1,
      },

      errors: [],
    };
  },

  created() {
    this.preloader = false;
  },

  mounted() { },

  computed: {},
  methods: {
    get_multiple_amount(val) {
      this.posting.amount = val * this.posting.qty;
    },

    getPercentage(amount, clause) {
      let res = (amount / 100) * clause;
      return res;
    },

    get_amount_with_tax(clause) {
      let per = 0;
      if (clause == "Food") {
        per = 5;
      } else if (clause == "Misc" || clause == "ExtraBed") {
        per = 12;
      }
      let res = this.getPercentage(this.posting.amount || 0, per);
      let gst = parseFloat(res) / 2;
      this.posting.sgst = gst;
      this.posting.cgst = gst;
      this.posting.tax = res;
      let a = parseFloat(res) + parseFloat(this.posting.amount || 0);
      this.posting.amount_with_tax = a.toFixed(2);
    },

    store_posting() {
      if (
        this.posting.amount_with_tax == 0 ||
        this.posting.item == "" ||
        this.posting.bill_no == "" ||
        this.posting.tax_type == -1
      ) {
        alert("Please enter required fields");
        return;
      }
      this.loading = true;
      let per = this.posting.tax_type == "Food" ? 5 : 12;
      let payload = {
        ...this.posting,
        booked_room_id: this.evenIid,
        company_id: this.$auth.user.company.id,
        booking_id: this.BookingData.id,
        room_id: this.BookingData.room_id,
        room: this.BookingData.room_no,
        tax_type: per,
      };
      this.$axios
        .post("/posting", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.reset_posting();
            this.closeDialog(data);
            this.postingDialog = false;
            this.loading = false;
            this.snackbar = data.status;
            this.response = data.message;
          }
        })
        .catch((e) => console.log(e));
    },

    closeDialog(data) {
      this.$emit("close-dialog", data);
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    reset_posting() {
      this.posting = {
        item: "",
        qty: "",
        amount: 0,
        single_amt: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1,
      };
    },
    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>

<style scoped src="@/assets/custom/checkout.css"></style>
