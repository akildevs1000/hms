<template>
  <div>
    <v-row>
      <v-col md="5">
        <v-card class="pa-5 mt-0" elevation="0">
          <h5>Customer</h5>
          <hr />
          <table>
            <tbody>
              <tr>
                <th class="no-bg">Name</th>
                <td class="no-bg">
                  {{ (customer && customer.title) || "---" }}
                  {{ (customer && customer.full_name) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Mobile</th>
                <td class="no-bg">
                  {{ (customer && customer.contact_no) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Whatsapp</th>
                <td class="no-bg">
                  {{ (customer && customer.whatsapp) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Email</th>
                <td class="no-bg">
                  {{ (customer && customer.email) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">address</th>
                <td class="no-bg">
                  {{ (customer && customer.address) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Whatsapp</th>
                <td class="no-bg">
                  {{ (customer && customer.whatsapp) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Nationality</th>
                <td class="no-bg">
                  {{ (customer && customer.nationality) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Id Card No</th>
                <td class="no-bg">
                  {{ (customer && customer.id_card_no) || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Car No</th>
                <td class="no-bg">
                  {{ (customer && customer.car_no) || "---" }}
                </td>
              </tr>
            </tbody>
          </table>
          <br />
          <br />
          <h5>Reservation</h5>
          <hr />
          <div>
            <v-row>
              <v-col cols="3"><b>Reservation No </b></v-col>
              <v-col cols="3">{{ (booking && booking.id) || "---" }}</v-col>
              <v-col cols="4"><b>Number of Rooms </b></v-col>
              <v-col cols="2">{{
                (bookedRooms && bookedRooms.length) || "---"
              }}</v-col>
            </v-row>
            <v-row>
              <v-col cols="3"><b>Check In </b></v-col>
              <v-col cols="3">{{
                (booking && booking.check_in_date) || "---"
              }}</v-col>
              <v-col cols="2"><b>Check Out </b></v-col>
              <v-col cols="4">{{
                (booking && booking.check_out_date) || "---"
              }}</v-col>
            </v-row>
            <v-row>
              <v-col cols="3"><b>Rooms Amount </b></v-col>
              <v-col cols="3"
                >{{ (booking && booking.total_price) || "0" }}.00</v-col
              >
              <v-col cols="2"><b>Days </b></v-col>
              <v-col cols="2">{{
                (booking && booking.total_days) || "---"
              }}</v-col>
            </v-row>

            <v-row>
              <v-col cols="3"><b>Remaining Amount</b></v-col>
              <v-col cols="4">
                <span>
                  {{ (booking && booking.remaining_price) || "0" }}.00</span
                >
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="3"><b>Posting Amount</b></v-col>
              <v-col cols="4">
                <span> {{ totalPostingAmount || "0" }}</span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="3"><b>Grand Remaining</b></v-col>
              <v-col cols="4">
                <span class="red--text">
                  {{
                    parseInt(totalPostingAmount) +
                      parseInt(booking && booking.remaining_price) || "0"
                  }}.00</span
                >
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="3"><b>Customer Request</b></v-col>
              <v-col cols="8">{{
                (booking && booking.request) || "---"
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
        <v-card class="mb-5 rounded-md" elevation="0">
          <table>
            <thead>
              <tr style="font-size:13px;background-color:#5FAFA3;color:white">
                <th>No</th>
                <th>Description</th>
                <th>Adults</th>
                <th>Child</th>
                <th>Babies</th>
                <th class="text-right">Price</th>
                <th class="text-right">After Discount</th>
                <th class="text-right">Sgst</th>
                <th class="text-right">Cgst</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in bookedRooms" :key="index">
              <tr style="font-size:13px">
                <td>{{ item.room_no || "---" }}</td>
                <td>{{ item.room_type || "---" }}</td>
                <td>{{ item.no_of_adult || "---" }}</td>
                <td>{{ item.no_of_child || "---" }}</td>
                <td>{{ item.no_of_baby || "---" }}</td>
                <td class="text-right">{{ item.price || "---" }}.00</td>
                <td class="text-right">
                  {{ item.after_discount || "---" }}.00
                </td>
                <td class="text-right">{{ item.sgst || "---" }}.00</td>
                <td class="text-right">{{ item.cgst || "---" }}.00</td>
                <td class="text-right">{{ item.total || "---" }}.00</td>
              </tr>
              <tr
                style="font-size:13px"
                v-for="(postingItem, postingIndex) in item.postings"
                :key="postingIndex"
              >
                <td>{{ item.room_no || "---" }}</td>
                <td>(Posting) {{ postingItem.item || "---" }}</td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right">{{ postingItem.amount || "---" }}</td>
                <td class="text-right">{{ postingItem.amount || "---" }}</td>
                <td class="text-right">{{ postingItem.sgst || "---" }}.00</td>
                <td class="text-right">{{ postingItem.cgst || "---" }}.00</td>
                <td class="text-right">
                  {{ postingItem.amount_with_tax || "---" }}
                </td>
              </tr>
            </tbody>
          </table>
        </v-card>
        <v-card class="mb-5 rounded-md" elevation="0">
          <table>
            <tr style="font-size:13px;background-color:#5FAFA3;color:white">
              <th>#</th>
              <th>Date</th>
              <th>Room</th>
              <th>Type</th>
              <th>Payment Mode</th>
              <th>Description</th>
              <th>Amount</th>
            </tr>
            <v-progress-linear
              v-if="loading"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
            <tr
              v-for="(item, index) in payments"
              :key="index"
              style="font-size:13px;"
            >
              <td>
                <b>{{ ++index }}</b>
              </td>
              <td>{{ item.created_at || "---" }}</td>
              <td>{{ item.room || "---" }}</td>
              <td>{{ item.type || "---" }}</td>
              <td>{{ (item && item.payment_mode.name) || "---" }}</td>
              <td>{{ item.description || "---" }}</td>
              <td class="text-right">{{ item.amount || "---" }}</td>
            </tr>
            <tr style="background-color:white"></tr>
            <tr style="background-color:white">
              <th colspan="7" class="text-right">{{ totalAmount }}.00</th>
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
    errors: [],
    totalAmount: 0,
    totalPostingAmount: 0
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

    calTotalAmount(payments) {
      let sum = 0;
      payments.forEach(item => {
        sum += parseInt(item.amount);
      });
      this.totalAmount = sum;
    },

    getData() {
      let id = this.$route.params.id;

      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        const booking = data.booking;
        this.customer = booking.customer;
        console.log(booking);
        this.booking = booking;
        this.payments = booking.payments;
        this.bookedRooms = booking.booked_rooms;
        //end booking
        this.loading = false;
        this.calTotalAmount(this.payments);
      });
    }
  }
};
</script>

<style scoped>
.no-bg {
  background-color: white !important;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
  /* border: 1px solid black !important; */
}

/* tr:nth-child(even) {
  background-color: #e9e9e9;
} */

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
