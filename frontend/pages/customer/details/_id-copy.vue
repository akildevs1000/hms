<template>
  <div>
    <v-row>
      <!-- <v-col md="3">
        <div class="profile-view">
          <div class="cover-image" style="background-color: #5fafa3"></div>
          <div class="profile-view-header">
            <div class="avatar-container">
              <img
                alt="Avatar"
                class="avatar"
                :src="customer.image || '/no-profile-image.jpg'"
              />
            </div>
            <h1 class="name">
              {{ customer.title || "---" }} {{ customer.full_name || "---" }}
            </h1>
            <div class=""></div>
          </div>

          <div class="profile-view-body">
            <v-card flat>
              <v-card-text>
                <div class="contact-info-container">
                  <ul class="contact-info">
                    <li>
                      <b class="label mr-3">Email :</b>
                      <a>
                        {{ (customer && customer.email) || "---" }}
                      </a>
                    </li>
                    <li>
                      <span class="label mr-3">Phone :</span>
                      {{ customer.contact_no || "---" }}
                    </li>
                    <li>
                      <span class="label mr-3">Whatsapp :</span>
                      {{ customer.whatsapp || "---" }}
                    </li>
                  </ul>
                  <ul class="contact-info">
                    <li>
                      <span class="label mr-3">Nationality :</span>
                      {{ customer.nationality || "---" }}
                    </li>

                    <li>
                      <span class="label mr-3">Car No :</span>
                      {{ customer.car_no || "---" }}
                    </li>
                    <li>
                      <span class="label mr-3">Date of Birth :</span>
                      {{ customer.dob || "---" }}
                    </li>
                    <li>
                      <span class="label mr-3">Address : </span>
                    </li>
                    {{
                      customer.address || "---"
                    }}
                  </ul>
                </div>
              </v-card-text>
            </v-card>
          </div>
        </div>
      </v-col> -->

      <v-col md="12">
        <v-card elevation="0">
          <v-toolbar color="cyan" dark flat>
            <v-app-bar-nav-icon></v-app-bar-nav-icon>
            <v-toolbar-title>Reservation Details</v-toolbar-title>
            <v-spacer></v-spacer>
            <template v-slot:extension>
              <v-tabs v-model="tab1" align-with-title>
                <v-tabs-slider color="yellow"></v-tabs-slider>

                <v-tab v-for="item in itemsCustomer" :key="item">
                  {{ item }}
                </v-tab>
              </v-tabs>
            </template>
          </v-toolbar>

          <v-tabs-items v-model="tab1">
            <v-tab-item class="px-3 py-4">
              <v-alert
                border="left"
                colored-border
                color="deep-purple accent-4"
                elevation="1"
              >
                <table>
                  <tr>
                    <td>Customer :</td>
                    <td>
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.title) ||
                        ""
                      }}.
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.full_name) ||
                        "---"
                      }}
                    </td>
                    <td>Contact No :</td>
                    <td>
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.contact_no) ||
                        "---"
                      }}
                    </td>
                    <td>Whatsapp :</td>
                    <td>
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.whatsapp) ||
                        "---"
                      }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6"><hr /></td>
                  </tr>
                  <tr>
                    <td>Reservation No :</td>
                    <td>{{ (booking && booking.reservation_no) || "---" }}</td>
                    <td>Number of Rooms :</td>
                    <td>{{ (bookedRooms && bookedRooms.length) || "---" }}</td>
                    <td>Booking Date :</td>
                    <td>{{ (booking && booking.booking_date) || "---" }}</td>
                  </tr>
                  <tr>
                    <td>Source :</td>
                    <td>{{ (booking && booking.source) || "---" }}</td>
                    <td>Reference Number :</td>
                    <td>{{ (booking && booking.reference_no) || "---" }}</td>
                    <td>Pay Type :</td>
                    <td>
                      {{
                        (booking && booking.paid_by == 2
                          ? "Paid By Agent"
                          : "Paid By Guest") || "---"
                      }}
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Check In :</td>
                    <td>{{ (booking && booking.check_in_date) || "---" }}</td>
                    <td>Check Out :</td>
                    <td>{{ (booking && booking.check_out_date) || "---" }}</td>
                    <td>Days :</td>
                    <td>{{ (booking && booking.total_days) || "---" }}</td>
                  </tr>
                  <tr>
                    <td colspan="6"><hr /></td>
                  </tr>
                  <tr class="bg-white">
                    <td>Room Amount :</td>
                    <td>{{ (booking && booking.total_price) || "0" }}</td>
                  </tr>
                  <tr class="bg-white">
                    <td>Posting Amount :</td>
                    <td>{{ totalPostingAmount || "0" }}</td>
                  </tr>
                  <tr class="bg-white">
                    <td>Remaining Amount :</td>
                    <td>{{ (booking && booking.remaining_price) || "0" }}</td>
                  </tr>
                  <tr class="bg-white">
                    <td>Grand Remaining :</td>
                    <td class="red--text">
                      {{ (booking && booking.grand_remaining_price) || "0" }}
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td colspan="6"><hr /></td>
                  </tr>
                  <tr class="bg-white">
                    <td>Customer Request :</td>
                    <td>
                      {{ (booking && booking.request) || "---" }}
                    </td>
                  </tr>
                  <tr class="bg-white" v-if="booking && booking.document">
                    <td>Document :</td>
                    <td class="red--text">
                      <v-btn
                        small
                        dark
                        class="primary pt-4 pb-4"
                        @click="preview(booking && booking.document)"
                      >
                        Preview
                        <v-icon right dark>mdi-file</v-icon>
                      </v-btn>
                    </td>
                  </tr>
                </table>
              </v-alert>

              <!-- <div>
                <v-row>
                  <v-col cols="3"><b>Reservation No </b></v-col>
                  <v-col cols="3">{{ (booking && booking.id) || "---" }}</v-col>
                  <v-col cols="4"><b>Number of Rooms </b></v-col>
                  <v-col cols="2">{{
                    (bookedRooms && bookedRooms.length) || "---"
                  }}</v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Source </b></v-col>
                  <v-col cols="3">{{
                    (booking && booking.source) || "---"
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
                  <v-col cols="3">{{
                    (booking && booking.total_price) || "0"
                  }}</v-col>
                  <v-col cols="2"><b>Days </b></v-col>
                  <v-col cols="2">{{
                    (booking && booking.total_days) || "---"
                  }}</v-col>
                </v-row>

                <v-row>
                  <v-row>
                    <v-col cols="3"><b>Posting Amount</b></v-col>
                    <v-col cols="4">
                      <span> {{ totalPostingAmount || "0" }}</span>
                    </v-col>
                  </v-row>
                  <v-col cols="3"><b>Remaining Amount</b></v-col>
                  <v-col cols="4">
                    <span>
                      {{ (booking && booking.remaining_price) || "0" }}</span
                    >
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Grand Remaining</b></v-col>
                  <v-col cols="4">
                    <span class="red--text">
                      {{
                        (booking && booking.grand_remaining_price) || "0"
                      }}</span
                    >
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Customer Request</b></v-col>
                  <v-col cols="8">{{
                    (booking && booking.request) || "---"
                  }}</v-col>
                </v-row>
                <v-row v-if="booking && booking.document">
                  <v-col cols="3"><b>Document</b></v-col>
                  <v-col cols="8">
                    <v-btn
                      small
                      dark
                      class="primary pt-4 pb-4"
                      @click="preview(booking && booking.document)"
                    >
                      Preview
                      <v-icon right dark>mdi-file</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
              </div> -->
            </v-tab-item>
            <v-tab-item class="px-3 py-4">
              <table class="responsive-table">
                <thead>
                  <tr class="table-header-text">
                    <th>No</th>
                    <th>Description</th>
                    <th>Adults</th>
                    <th>Child</th>
                    <th>Babies</th>
                    <th>Meal Plan</th>
                    <th>Adult Food Amount</th>
                    <th>Child Food Amount</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">After Discount</th>
                    <th class="text-right">Sgst</th>
                    <th class="text-right">Cgst</th>
                    <th class="text-right">Total</th>
                  </tr>
                </thead>
                <tbody v-for="(item, index) in bookedRooms" :key="index">
                  <tr style="font-size: 13px">
                    <td>{{ item.room_no || "---" }}</td>
                    <td>{{ item.room_type || "---" }}</td>
                    <td>{{ item.no_of_adult || "---" }}</td>
                    <td>{{ item.no_of_child || "---" }}</td>
                    <td>{{ item.no_of_baby || "---" }}</td>
                    <td>{{ capsTitle(item.meal) || "---" }}</td>
                    <td class="text-right">
                      {{ item.tot_adult_food || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.tot_child_food || "---" }}
                    </td>
                    <td class="text-right">{{ item.price || "---" }}</td>
                    <td class="text-right">
                      {{ item.room_discount || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.after_discount || "---" }}
                    </td>
                    <td class="text-right">{{ item.sgst || "---" }}</td>
                    <td class="text-right">{{ item.cgst || "---" }}</td>
                    <td class="text-right">{{ item.total || "---" }}</td>
                  </tr>
                  <tr
                    style="font-size: 13px"
                    v-for="(postingItem, postingIndex) in item.postings"
                    :key="postingIndex"
                  >
                    <td>{{ item.room_no || "---" }}</td>
                    <td>(Posting) {{ postingItem.item || "---" }}</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right">
                      {{ postingItem.amount || "---" }}
                    </td>
                    <td class="text-right">
                      {{ postingItem.amount || "---" }}
                    </td>
                    <td class="text-right">{{ postingItem.sgst || "---" }}</td>
                    <td class="text-right">{{ postingItem.cgst || "---" }}</td>
                    <td class="text-right">
                      {{ postingItem.amount_with_tax || "---" }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-tab-item>
            <v-tab-item class="px-3">
              <v-card flat>
                <v-row>
                  <v-col cols="12">
                    <table class="responsive-table mt-3">
                      <thead>
                        <tr class="table-header-text">
                          <th>#</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>payment Mode</th>
                          <th>Reference</th>
                          <th>Description</th>
                          <th>Debit</th>
                          <th>Credit</th>
                          <th>Balance</th>
                        </tr>
                      </thead>
                      <v-progress-linear
                        v-if="loading"
                        :active="loading"
                        :indeterminate="loading"
                        absolute
                        color="primary"
                      ></v-progress-linear>
                      <tbody>
                        <tr
                          v-for="(item, index) in transactions"
                          :key="index"
                          style="font-size: 13px"
                          class="no-bg"
                        >
                          <td>
                            <b>{{ ++index }}</b>
                          </td>
                          <td>{{ item.date || "---" }}</td>
                          <td>{{ item.time || "---" }}</td>
                          <td>
                            {{
                              (item &&
                                item.payment_mode &&
                                item.payment_mode.name) ||
                              "---"
                            }}
                          </td>
                          <td>{{ item.reference_number || "---" }}</td>
                          <td>{{ item.desc || "---" }}</td>
                          <td class="text-right">
                            {{ item && item.debit == 0 ? "---" : item.debit }}
                          </td>
                          <td class="text-right">
                            {{ item && item.credit == 0 ? "---" : item.credit }}
                          </td>
                          <td class="text-right">
                            {{ item.balance || "---" }}
                          </td>
                        </tr>
                      </tbody>
                      <tr style="font-size: 13px">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th class="text-right">Balance</th>
                        <td class="text-right">
                          {{ totalTransactionAmount }}
                        </td>
                      </tr>
                    </table>
                  </v-col>
                </v-row>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="7">
        <!-- <v-card class="mb-5 rounded-md" elevation="0">
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
              <th colspan="7" class="text-right">{{ totalAmount }}</th>
            </tr>
          </table>
        </v-card> -->
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
      per_page: 10,
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
    itemsCustomer: ["Reservation", "Room", "Transaction"],
    tab1: null,
    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",

    tab: null,
    headers: [
      {
        text: "#",
      },
      {
        text: "Type",
      },
      {
        text: "Source",
      },
      {
        text: "Rooms",
      },
      {
        text: "Booking Date",
      },
      {
        text: "Check In",
      },
      {
        text: "Check Out",
      },
      {
        text: "Total Price",
      },
    ],
    payments: [],
    booking: [],
    bookedRooms: [],
    transactions: [],
    errors: [],
    totalAmount: 0,
    totalPostingAmount: 0,
    totalTransactionAmount: 0,
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
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
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

    getData() {
      let id = this.$route.params.id;

      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        this.totalTransactionAmount = data.totalTransactionAmount;
        this.transactions = data.transaction;
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
    },
  },
};
</script>
<style scoped src="@/assets/custom.css"></style>

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

tr:nth-child(even) {
  background-color: white;
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
.table-header-text {
  font-size: 12px;
}
</style>
