<template>
  <div>
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
          {{ BookingData && BookingData.check_in }}
        </td>
      </tr>
      <tr>
        <th>Check Out</th>
        <td>
          {{ BookingData && BookingData.check_out }}
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
        <th>Remaining Balance</th>
        <td>{{ BookingData.grand_remaining_price }}</td>
      </tr>

      <tr style="background-color: white">
        <th>
          New Advance
          <span class="text-danger">*</span>
        </th>
        <td>
          <v-text-field
            dense
            outlined
            type="number"
            v-model="new_advance"
            :hide-details="true"
          ></v-text-field>
        </td>
      </tr>
      <tr></tr>
    </table>
    <v-btn
      class="primary mt-2"
      small
      @click="store_advance(BookingData)"
      :loading="loading"
      >Pay</v-btn
    >
  </div>
</template>
<script>
export default {
  props: ["BookingData"],
  data() {
    return {
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      reference: "",
      errors: [],
      checkOutDialog: false,
    };
  },

  created() {
    this.preloader = false;
  },

  mounted() {},

  computed: {},

  methods: {
    store_advance(data) {
      if (this.new_advance == "") {
        alert("Enter advance amount");
        return;
      }
      this.loading = true;
      let payload = {
        new_advance: this.new_advance,
        reference_number: this.reference,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
        company_id: this.$auth.user.company.id,
      };
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            console.log("ff");
            this.loading = false;
          }
        })
        .catch((e) => console.log(e));
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
