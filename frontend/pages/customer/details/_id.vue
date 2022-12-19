<template>
  <div>
    <v-row>
      <v-col md="5">
        <v-card class="pa-5 mt-0" elevation="0">
          <h5>{{ customer.full_name }}</h5>
          <hr />
          <span class="text-center">
            <b>Mobile : </b>{{ customer.contact_no }} | <b>Mobile : </b
            >{{ customer.email }}
          </span>
          <br />
          <br />
          <br />
          <h5>Reservation</h5>
          <hr />
          <div>
            <v-row>
              <v-col cols="3"><b>Reservation No :</b></v-col>
              <v-col cols="3">{{ (booking && booking.id) || "---" }}</v-col>
              <v-col cols="4"><b>Number of Rooms :</b></v-col>
              <v-col cols="2">{{ bookedRooms.length || "---" }}</v-col>
            </v-row>
            <v-row>
              <v-col cols="2"><b>Check In :</b></v-col>
              <v-col cols="4">{{
                (booking && booking.check_in_date) || "---"
              }}</v-col>
              <v-col cols="2"><b>Check Out :</b></v-col>
              <v-col cols="4">{{
                (booking && booking.check_out_date) || "---"
              }}</v-col>
            </v-row>
            <v-row>
              <v-col cols="3"><b>Rooms Amount :</b></v-col>
              <v-col cols="3"
                >{{ (booking && booking.total_price) || "00" }}.00</v-col
              >
              <v-col cols="2"><b>Days :</b></v-col>
              <v-col cols="2">{{
                (booking && booking.total_days) || "---"
              }}</v-col>
            </v-row>
          </div>
          <br /><br />
          <h5>Booked Rooms</h5>
          <hr />
          <div>
            <table>
              <tr style="font-size:13px">
                <th>No</th>
                <th>Type</th>
                <th>Price</th>
                <th>After Discount</th>
                <th>Sgst</th>
                <th>Cgst</th>
                <th>Total</th>
              </tr>
              <tbody>
                <tr
                  v-for="(item, index) in bookedRooms"
                  :key="index"
                  style="font-size:13px"
                >
                  <td>{{ item.room_no || "---" }}</td>
                  <td>{{ item.room_type || "---" }}</td>
                  <td>{{ item.price || "---" }}.00</td>
                  <td>{{ item.after_discount || "---" }}.00</td>
                  <td>{{ item.sgst || "---" }}.00</td>
                  <td>{{ item.cgst || "---" }}.00</td>
                  <td>{{ item.total || "---" }}.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </v-card>
      </v-col>
      <v-col md="7">
        <v-card class="mb-5 rounded-md " elevation="0">
          <table>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Payment Mode</th>
              <th>Payment Type</th>
              <th>Amount</th>
            </tr>
            <v-progress-linear
              v-if="loading"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
            <tr v-for="(item, index) in payments" :key="index">
              <td>
                <b>{{ ++index }}</b>
              </td>
              <td>{{ item.created_at }}</td>
              <td>{{ item.payment_mode }}</td>
              <td>{{ item.description }}</td>
              <td>{{ item.amount }}</td>
              <td>
                <!-- <v-icon
                  @click="viewCustomerBilling(item.id)"
                  x-small
                  color="primary"
                  class="mr-2"
                >
                  mdi-eye
                </v-icon> -->
              </td>
            </tr>
          </table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    Model: "Customer",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    response: "",
    customer: [],
    payments: [],
    booking: [],
    bookedRooms: [],
    errors: []
  }),

  computed: {},
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() {},

  methods: {
    getDate(dataTime) {
      return dataTime;
      // return new Date(dataTime.toDateString());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    getData() {
      let id = this.$route.params.id;
      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        this.customer = data;
        this.booking = data.booking;
        this.payments = data.booking.payments;
        this.bookedRooms = data.booking.booked_rooms;
        this.loading = false;
      });
    }
  }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}
input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}
</style>
