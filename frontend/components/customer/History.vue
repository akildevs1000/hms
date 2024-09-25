<template>
  <v-container>
    <v-row>
      <v-col v-for="(stat, index) in stats" :key="index">
        <v-card rounded="lg" outlined class="pa-2">
          <v-row no-gutter>
            <v-col cols="2" class="pt-5">
              <v-icon size="30" color="black">{{ stat.icon }}</v-icon>
            </v-col>
            <v-col class="text-center">
              <AssetsTextLabel color="black" :label="stat.value" />
              <br>
              <AssetsTextLabel :label="stat.label" />
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="12" v-if="bookings.length">
        <AssetsTable :headers="headers" :items="bookings" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import PaidBookedSvg from "../svg/PaidBookedSvg.vue";
import Available from "../svg/Available.vue";
export default {
  components: {
    Available,
    PaidBookedSvg,
  },
  props: ["customerId"],
  data() {
    return {
      stats: [],
      customer: "",
      bookings: [],
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      viewportWidth: 0,
      viewportHeight: 0,
      headers: [],
    };
  },
  watch: {
    customerId() {
      this.get_customer_history();
    },
  },
  mounted() {
    this.get_customer_history();

    this.viewportWidth = window.innerWidth;
    this.viewportHeight = window.innerHeight;

    window.addEventListener("resize", () => {
      this.viewportWidth = window.innerWidth;
      this.viewportHeight = window.innerHeight;
    });
  },
  methods: {
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

    get_customer_history() {
      this.$axios
        .get(`get_customer_history/${this.customerId}`)
        .then(({ data }) => {
          this.customer = data.data;

          this.headers = [
            // { text: `#`, value: `serial`, align: `center` },
            { text: `Rev. No`, value: `reservation_no`, align: `center` },
            // { text: `Type`, value: `type`, align: `center` },
            // { text: `Source`, value: `source`, align: `center` },
            { text: `Room`, value: `room`, align: `center` },
            // { text: `Cat`, value: `category`, align: `center` },
            // { text: `Booking Date`, value: `booking_date`, align: `center` },
            { text: `Check In`, value: `check_in`, align: `center` },
            { text: `Check Out`, value: `check_out`, align: `center` },
            { text: `Total Price`, value: `total_price`, align: `right` },
            { text: `Paid`, value: `paid`, align: `right` },
            { text: `Balance`, value: `balance`, align: `right` },

          ];
          this.bookings = data.data.bookings.map((e, i) => ({
            // serial: i + 1,
            reservation_no: e.reservation_no, // @click="goToRevView(item)"
            // type: e.type,
            // source: e.source,
            room: e.booked_rooms && e.booked_rooms.length ? e.booked_rooms.map(e => e.room_no).join(",")  : "",
            // category: e.booked_rooms && e.booked_rooms.length ? e.booked_rooms.map(e => e.room_type).join(",")  : "",
            booking_date: this.$dateFormat.dmy(e.booking_date),
            check_in: this.$dateFormat.dmy(e.check_in),
            check_out: this.$dateFormat.dmy(e.check_out),
            total_price: this.$utils.currency_format(e.total_price),
            paid: this.$utils.currency_format(e.paid_amounts),
            balance: this.$utils.currency_format(e.balance),
          }));

          this.stats = [
            {
              icon: "mdi-car",
              label: "Number of Visit",
              value: this.bookings.length,
            },
            {
              icon: "mdi-bed",
              label: "Number of Nights",
              value: data.data.order_rooms_count,
            },
            {
              icon: "mdi-cash",
              label: "Revenue",
              value: this.$utils.currency_format(data.revenue),
            },
            {
              icon: "mdi-wallet",
              label: "City Ledger",
              value: this.$utils.currency_format(data.city_ledger),
            },
          ];
        });
    },
  },
};
</script>
