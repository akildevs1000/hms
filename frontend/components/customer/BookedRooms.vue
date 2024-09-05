<template>
  <span>
    <table cellspacing="0" style="width: 100%">
      <thead style="background-color: #f2f2f2; width: 100%">
        <tr style="background-color: #f2f2f2; width: 100%">
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Date</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Room</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Tariff</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Adult</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Child</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Meal</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>E. Bed</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>E. C/in</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>L. C/out</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Total</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
          ></td>
        </tr>
      </thead>
      <tbody v-for="(item, index) in orderRooms" :key="index">
        <tr style="font-size: 13px">
          <td class="text-center py-2">{{ item.date || "---" }}</td>
          <td class="text-center py-2">
            {{ item.room_no || "---" }} {{ item.room_type || "---" }}
          </td>
          <td class="text-center py-2">{{ item.tariff }}</td>
          <td class="text-center py-2">{{ item.no_of_adult }}</td>
          <td class="text-center py-2">{{ item.no_of_child }}</td>
          <td class="text-center py-2">
            {{
              item?.foodplan?.title +
                " (" +
                item?.foodplan?.unit_price +
                ") " || "---"
            }}
          </td>
          <td class="text-center py-2">
            {{ item.bed_amount || "---" }}
          </td>
          <td class="text-center py-2">
            {{ item.early_check_in || "---" }}
          </td>
          <td class="text-center py-2">
            {{ item.late_check_out || "---" }}
          </td>
          <td class="text-center py-2">{{ item.total || "---" }}</td>
        </tr>
      </tbody>
    </table>

    <div class="d-flex justify-space-around py-3" style="margin-top: 5px">
      <v-col cols="10" class="text-right">
        <div>Sub Total:</div>
        <div>Add :</div>
        <div>Discount :</div>
        <div style="font-size: 18px; font-weight: bold">Total :</div>
      </v-col>
      <v-col cols="2" class="text-right">
        <div>
          {{ convert_decimal(subTotal()) }}
        </div>

        <div>
          {{ convert_decimal(extraAmount()) }}
        </div>
        <div style="color: red">-{{ bookingDiscount() }}</div>
        <div style="font-size: 18px; font-weight: bold">
          {{ convert_decimal(processCalculation()) }}
        </div>
      </v-col>
    </div>

    <!-- <v-row class="mt-3">
      <v-col md="2" sm="12" cols="12" dense>
        <v-select
          label="Discount/Extra"
          v-model="extraPayType"
          :items="['Discount', 'ExtraAmount']"
          dense
          :hide-details="true"
          outlined
        ></v-select>
      </v-col>
      <v-col md="4" sm="12" cols="12" dense v-if="extraPayType == 'Discount'">
        <v-text-field
          label="Discount Amount"
          dense
          outlined
          type="number"
          v-model="room_discount"
          :hide-details="true"
          @keyup="processCalculation"
        ></v-text-field>
      </v-col>
      <v-col md="4" sm="12" cols="12" dense v-if="extraPayType == 'Discount'">
        <v-text-field
          label="Reason"
          dense
          outlined
          type="text"
          v-model="discount_reason"
          :hide-details="true"
        ></v-text-field>
      </v-col>
      <v-col
        md="4"
        sm="12"
        cols="12"
        dense
        v-if="extraPayType == 'ExtraAmount'"
      >
        <v-text-field
          label="Extra Amount"
          dense
          outlined
          type="number"
          v-model="room_extra_amount"
          @keyup="processCalculation"
          :hide-details="true"
        ></v-text-field>
      </v-col>
      <v-col
        md="4"
        sm="12"
        cols="12"
        dense
        v-if="extraPayType == 'ExtraAmount'"
      >
        <v-text-field
          label="Reason"
          dense
          outlined
          type="text"
          v-model="extra_amount_reason"
          :hide-details="true"
        ></v-text-field>
      </v-col>
    </v-row>
    <v-row> -->
    <v-row class="mt-3">
      <v-col class="text-right">
        <!-- <v-btn
                              :disabled="!customerDocs"
                                @click="advanceDialog = true"
                                color="primary"
                                >Pay</v-btn
                              > -->

        <v-btn
          small
          class="blue"
          @click="storeSubCustomer"
          :loading="loading"
          dark
          ><v-icon small class="mt-1">mdi-floppy</v-icon> Check In</v-btn
        >
      </v-col>
    </v-row>
  </span>
</template>
<script>
export default {
  props: ["customer", "BookingData"],
  data: () => ({
    roomTypeColor: "primary",
    extraPayType: "",

    discount: 0,
    hallRentPerHour: 0,
    projecterTotalAmount: 0,
    cleaningTotalAmount: 0,
    electricityTotalAmount: 0,
    audioTotalAmount: 0,
    tax_percentage: 0,
    room_tax_amount: 0,
    hallItemsTotal: 0,

    durationInHours: 1,
    foodTotalAmount: 0,

    hallRentTotalAmount: 0,
    otherCharges: 0,

    AmountGrandTotal: 0,
    hallTaxableTotalAmount: 0,
    inv_total_without_tax: 0,
    inv_total: 0,
    inv_total_tax: 0,
    discount: 0,

    hours: [
      { id: 9, name: "09 AM" },
      { id: 10, name: "10 AM" },
      { id: 11, name: "11 AM" },
      { id: 12, name: "12 PM" },
      { id: 13, name: "01 PM" },
      { id: 14, name: "02 PM" },
      { id: 15, name: "03 PM" },
      { id: 16, name: "04 PM" },
      { id: 17, name: "05 PM" },
      { id: 18, name: "06 PM" },
      { id: 19, name: "07 PM" },
      { id: 20, name: "08 PM" },
      { id: 21, name: "09 PM" },
      { id: 22, name: "10 PM" },
      { id: 23, name: "11 PM" },
    ],

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    documentObj: {
      fileExtension: null,
      file: null,
    },
    options: {},
    imgView: false,
    showImage: "",
    Model: "Customer",
    search: "",
    GRCDialog: false,
    snackbar: false,
    dialog: false,
    payingDialog: false,
    ids: [],
    payData: {},
    loading: false,
    response: "",
    itemsCustomer: [
      "Reservation",
      "Room",
      "Postings",
      "Transaction",
      "Online Booking",
    ],
    tab1: null,
    activeTab: 0,

    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",

    tab: null,
    payments: [],
    booking: [],
    bookedRooms: [],
    orderRooms: [],
    postings: [],
    transactions: [],
    transactionSummary: [],
    errors: [],
    totalAmount: 0,
    totalPostingAmount: 0,
    totalTransactionAmount: 0,
    room_category_type: "",
    food: [],
    extra_amounts: [],
    bookingData: 0,
    room_discount: 0,
    room_extra_amount: 0,
  }),

  computed: {},
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() {},

  methods: {
    storeSubCustomer() {
      let payload = {
        booking_id: this.BookingData.id,
        new_payment: 0,
        customer_id: this.customer.id,
        room_id: this.BookingData.room_id,
        ...this.customer,
      };
      console.log("🚀 ~ storeSubCustomer ~ payload:", payload);

      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.$emit("checkedin-success");

            this.loading = false;
          }
        })
        .catch((e) => {
          this.loading = false;
        });
    },
    processCalculation() {
      return this.bookingData.total_price;
      // let discount = parseFloat(this.room_discount) || 0;
      // let room_extra_amount = parseFloat(this.room_extra_amount) || 0;
      // let sub_total = parseFloat(this.sub_total) || 0;

      // let afterExtraAmount = sub_total + room_extra_amount;
      // let afterDiscount = afterExtraAmount - discount;

      // return (this.total_price = afterDiscount);
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
    subTotal() {
      return this.bookingData.sub_total;
      // return (this.BookingData.sub_total = this.priceListTableView.reduce(
      //   (total, num) => total + num.total_price,
      //   0
      // ));
    },
    extraAmount() {
      return this.bookingData.total_extra;
    },
    bookingDiscount() {
      return this.bookingData.discount;
    },
    closeDialogs() {
      this.getData();
      this.payingDialog = false;
    },

    numFormat(num) {
      if (!num) return "0";

      const number = num;
      const res = number.toFixed(2);
      return res;
      const formatted = number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      return formatted;
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      let title = upper.replace(/[^A-Z]/g, " ");
      return title.replace("PRICE", "");
    },

    calTotalAmount(payments) {
      let sum = 0;
      payments.forEach((item) => {
        sum += parseFloat(item.amount);
      });
      this.totalAmount = sum;
    },

    redirect() {
      this.$router.push("/hotel/calendar1");
    },

    get_payment() {
      this.payData = this.booking;

      // {
      //   id: this.$route.params.id,
      // };
      this.payingDialog = true;
    },

    getData() {
      let id = this.customer.id;
      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        this.totalTransactionAmount = data.totalTransactionAmount;
        this.transactions = data.transaction;
        this.transactionSummary = data.transactionSummary;
        this.bookingData = data.booking;
        const booking = data.booking;

        this.booking = booking;

        this.payments = booking.payments;
        this.bookedRooms = booking.booked_rooms;
        this.orderRooms = booking.order_rooms;
        this.postings = data.postings;
        //end booking
        this.loading = false;
        this.showImage = data.booking.customer.image;
        this.calTotalAmount(this.payments);
        this.room_category_type = data.booking.room_category_type;
      });
    },
    getPriceFormat(price) {
      return parseFloat(price).toLocaleString("en-IN", {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
      });
    },
  },
};
</script>
