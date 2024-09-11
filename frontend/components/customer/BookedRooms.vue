<template>
  <span v-if="orderRoomData && orderRoomData.id">
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
            <small>Check In</small>
          </td>
          <td
            style="
              border-top: 1px solid #bdbdbd;
              border-bottom: 1px solid #bdbdbd;
            "
            class="text-center py-2"
          >
            <small>Check Out</small>
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
      <tbody>
        <tr style="font-size: 13px">
          <td class="text-center py-2">
            {{ orderRoomData.check_in + " 12:00" || "---" }}
          </td>
          <td class="text-center py-2">
            {{ orderRoomData.check_out + " 11:00" || "---" }}
          </td>
          <td class="text-center py-2">
            {{ orderRoomData.room_no || "---" }}
          </td>
          <td class="text-center py-2">
            {{ orderRoomData.room_type || "---" }}
          </td>
          <td class="text-center py-2">{{ orderRoomData.no_of_adult }}</td>
          <td class="text-center py-2">{{ orderRoomData.no_of_child }}</td>

          <td class="text-center py-2">
            {{ orderRoomData?.foodplan?.title || "---" }}
          </td>
          <td class="text-center py-2">
            {{ orderRoomData.extra_bed_qty || "---" }}
          </td>
          <td class="text-center py-2">
            {{ orderRoomData.early_check_in || "---" }}
          </td>

          <td class="text-center py-2">
            {{ orderRoomData.late_check_out || "---" }}
          </td>

          <td class="text-center py-2">
            {{ orderRoomData.total }}
          </td>
        </tr>
      </tbody>
    </table>

    <!-- <div class="d-flex justify-space-around py-3" style="margin-top: 5px">
      <v-col cols="10" class="text-right">
        <div>Sub Total:</div>
        <div>Discount :</div>
        <div style="font-size: 18px; font-weight: bold">Total :</div>
      </v-col>
      <v-col cols="2" class="text-right">
        <div>
          {{ convert_decimal(subTotal()) }}
        </div>
        <div style="color: red">
          <v-hover v-slot:default="{ hover, props }">
            <div v-bind="props">
              -{{ convert_decimal(customer?.latest_booking?.discount || 0) }}
              <v-icon
                v-if="hover"
                small
                color="primary"
                @click="$refs[`DiscountComp`][`discountPopUp`] = true"
                >mdi-pencil</v-icon
              >
              <Discount
                ref="DiscountComp"
                :sub_total="customer?.latest_booking?.sub_total"
                @discountAbleAmount="
                  (e) => {
                    discount = e;
                  }
                "
              />
            </div>
          </v-hover>
        </div>
        <div style="font-size: 18px; font-weight: bold">
          {{ convert_decimal(processCalculation()) }}
        </div>
      </v-col>
    </div> -->

    <v-row class="mt-3">
      <v-col class="text-right">
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
    room_discount: 0,
    room_extra_amount: 0,
    orderRoomData: {},
    discount:0,
  }),

  computed: {},
  async created() {
    this.BookedRoomData = this.BookingData;

    await this.getOrderRoomData(this.BookedRoomData.id);
  },
  mounted() {},

  methods: {
    async getOrderRoomData(id = 0) {
      let { data } = await this.$axios.get(`get-order-room-data/${id}`);

      this.orderRoomData = data;
    },

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
      return this.BookingData.total_price;
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
      return this.customer.latest_booking.sub_total;
    },
    extraAmount() {
      return 0;
    },
    bookingDiscount() {
      return 0;
    },
    closeDialogs() {
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
    getPriceFormat(price) {
      return parseFloat(price).toLocaleString("en-IN", {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
      });
    },
  },
};
</script>
