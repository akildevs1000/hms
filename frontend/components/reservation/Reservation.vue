<template>
  <div>
    <v-card class="px-2" elevation="0">
      <v-container>
        <v-row>
          <v-col cols="12">
            <v-row>
              <div class="text-center ma-2">
                <!-- <v-snackbar top="top" color="secondary" elevation="24"> -->
                <v-snackbar
                  top="top"
                  v-model="snackbar"
                  color="secondary"
                  elevation="24"
                >
                  {{ response }}
                </v-snackbar>
              </div>
              <v-card-title primary-title> Room Information </v-card-title>
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Type <span class="error--text">*</span></label
                >
                <v-autocomplete
                  v-model="room.type"
                  :items="types"
                  dense
                  outlined
                  @change="getType(room.type)"
                  :hide-details="!errors.type"
                  :error="errors.type"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                ></v-autocomplete>
              </v-col>
              <v-col md="6" cols="12" sm="12" v-if="isAgent">
                <label class="col-form-label">Agent Name</label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="room.agent_name"
                  :hide-details="!errors.agent_name"
                  :error="errors.agent_name"
                  :error-messages="
                    errors && errors.agent_name ? errors.agent_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense v-if="isOnline">
                <label class="col-form-label">Source </label>
                <v-autocomplete
                  v-model="room.source"
                  :items="sources"
                  dense
                  outlined
                  :hide-details="!errors.source"
                  :error="errors.source"
                  :error-messages="
                    errors && errors.source ? errors.source[0] : ''
                  "
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Room Type <span class="error--text">*</span></label
                >
                <v-text-field
                  v-model="reservation.room_type"
                  readonly
                  dense
                  outlined
                  :hide-details="!errors.room_type"
                  :error="errors.room_type"
                  :error-messages="
                    errors && errors.room_type ? errors.room_type[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label">Room No </label>
                <v-text-field
                  v-model="reservation.room_no"
                  readonly
                  dense
                  outlined
                  :hide-details="!errors.room_id"
                  :error="errors.room_id"
                  :error-messages="
                    errors && errors.room_id ? errors.room_id[0] : ''
                  "
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="6" md="6">
                {{ rooms.start }}
                <label class="col-form-label">Check In </label>
                <v-menu
                  v-model="check_in_menu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="reservation.check_in"
                      readonly
                      v-bind="attrs"
                      :hide-details="true"
                      v-on="on"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="reservation.check_in"
                    @input="check_in_menu = false"
                    @change="getDays"
                    readonly
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col cols="12" sm="6" md="6">
                {{ rooms.end }}
                <label class="col-form-label">Check Out </label>
                <v-menu
                  v-model="check_out_menu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="reservation.check_out"
                      readonly
                      v-bind="attrs"
                      :hide-details="true"
                      v-on="on"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="reservation.check_out"
                    @input="check_out_menu = false"
                    @change="runAllFunctions"
                    readonly
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <label class="col-form-label">Discount</label>
                <v-text-field
                  @keyup="runAllFunctions"
                  type="number"
                  dense
                  :hide-details="true"
                  outlined
                  v-model="room.discount"
                ></v-text-field>
              </v-col>

              <v-col md="4" cols="12" sm="12">
                <label class="col-form-label">Advance Price</label>
                <v-text-field
                  @keyup="runAllFunctions"
                  dense
                  :hide-details="true"
                  outlined
                  type="number"
                  v-model="room.advance_price"
                ></v-text-field>
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <label class="col-form-label">Payment Mode</label>
                <v-autocomplete
                  v-model="room.payment_mode_id"
                  :items="[
                    { id: 1, name: 'Cash' },
                    { id: 2, name: 'Card' },
                    { id: 3, name: 'Online' },
                    { id: 4, name: 'Bank' },
                    { id: 5, name: 'UPI' },
                    { id: 6, name: 'Cheque' }
                  ]"
                  item-text="name"
                  item-value="id"
                  dense
                  outlined
                  @change="runAllFunctions"
                  :hide-details="!errors.payment_mode_id"
                  :error="errors.payment_mode_id"
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12">
            <v-divider></v-divider>
            <v-row>
              <v-card-title primary-title> Billing Information </v-card-title>
              <v-btn
                color="primary ml-4"
                style="width:130px"
                @click="
                  () => {
                    runAllFunctions();
                    reservation.isCalculate = false;
                  }
                "
                >Calculate</v-btn
              >
              <v-col cols="12">
                <table>
                  <tr>
                    <td>Total Days</td>
                    <td>
                      <div align="right">{{ getDays() }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Room Price</td>
                    <td>
                      <div align="right">{{ reservation.price }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Sub Total (Total Days x Room Price) ({{ getDays() }} x
                      {{ reservation.price }})
                    </td>
                    <td>
                      <div align="right">{{ room.sub_total }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Discount</td>
                    <td>
                      <div align="right">{{ room.discount }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>After Discount</td>
                    <td>
                      <div align="right">{{ room.after_discount }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Sales Tax</td>
                    <td>
                      <div align="right">{{ room.sales_tax }}</div>
                    </td>
                  </tr>

                  <tr>
                    <th>Total</th>
                    <td>
                      <div align="right">{{ room.total_price }}</div>
                    </td>
                  </tr>

                  <tr>
                    <td>Advance Payment</td>
                    <td>
                      <div align="right">{{ room.advance_price }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Remaining Amount</td>
                    <td>
                      <div align="right">{{ room.remaining_price }}</div>
                    </td>
                  </tr>
                </table>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12">
            <v-divider></v-divider>
            <v-row>
              <v-card-title primary-title> Customer Information </v-card-title>
              <v-col md="8" cols="12" sm="12">
                <label class="col-form-label"
                  >Contact No <span class="error--text">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="customer.contact_no"
                  :hide-details="!errors.contact_no"
                  :error="errors.contact_no"
                  :error-messages="
                    errors && errors.contact_no ? errors.contact_no[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="4" class="mt-10" cols="12" sm="12">
                <label class="col-form-label"></label>
                <v-btn
                  color="primary"
                  @click="get_customer()"
                  :loading="checkLoader"
                  >Check Customer</v-btn
                >
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <label class="col-form-label"
                  >First Name <span class="error--text">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.first_name"
                  :hide-details="!errors.first_name"
                  :error="errors.first_name"
                  :error-messages="
                    errors && errors.first_name ? errors.first_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <label class="col-form-label">Last Name</label>
                <v-text-field
                  dense
                  :hide-details="true"
                  outlined
                  type="text"
                  v-model="customer.last_name"
                ></v-text-field>
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <label class="col-form-label"
                  >Email <span class="error--text">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="email"
                  v-model="customer.email"
                  :hide-details="!errors.email"
                  :error="errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="4" sm="12" cols="12" dense>
                <label class="col-form-label">Adult </label>
                <v-autocomplete
                  v-model="customer.no_of_adult"
                  :items="member_numbers"
                  dense
                  outlined
                  :hide-details="!errors.no_of_adult"
                  :error="errors.no_of_adult"
                  :error-messages="
                    errors && errors.no_of_adult ? errors.no_of_adult[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col>
              <v-col md="4" sm="12" cols="12" dense>
                <label class="col-form-label">Child </label>
                <v-autocomplete
                  v-model="customer.no_of_child"
                  :items="member_numbers"
                  :hide-details="true"
                  dense
                  outlined
                >
                </v-autocomplete>
              </v-col>
              <v-col md="4" sm="12" cols="12" dense>
                <label class="col-form-label">Baby </label>
                <v-autocomplete
                  v-model="customer.no_of_baby"
                  :items="member_numbers"
                  dense
                  :hide-details="true"
                  outlined
                >
                </v-autocomplete>
              </v-col>
              <v-col md="3" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >ID Card Type <span class="error--text">*</span></label
                >
                <v-autocomplete
                  v-model="customer.id_card_type_id"
                  :items="idCards"
                  dense
                  outlined
                  item-text="name"
                  item-value="id"
                  :hide-details="!errors.id_card_type_id"
                  :error="errors.id_card_type_id"
                  :error-messages="
                    errors && errors.id_card_type_id
                      ? errors.id_card_type_id[0]
                      : ''
                  "
                ></v-autocomplete>
              </v-col>
              <v-col md="3" cols="12" sm="12">
                <label class="col-form-label"
                  >Selected ID Card Number
                  <span class="error--text">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.id_card_no"
                  :hide-details="!errors.id_card_no"
                  :error="errors.id_card_no"
                  :error-messages="
                    errors && errors.id_card_no ? errors.id_card_no[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12" sm="12">
                <label class="col-form-label">GST</label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.gst_number"
                  :hide-details="!errors.gst_number"
                  :error="errors.gst_number"
                  :error-messages="
                    errors && errors.gst_number ? errors.gst_number[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12" sm="12">
                <label class="col-form-label">Car Number</label>
                <v-text-field
                  dense
                  outlined
                  :hide-details="true"
                  type="text"
                  v-model="customer.car_no"
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <label class="col-form-label">Address </label>
                <v-textarea
                  rows="3"
                  v-model="customer.address"
                  outlined
                  :hide-details="true"
                ></v-textarea>
              </v-col>
              <v-col cols="6">
                <label class="col-form-label">Customer Request </label>
                <v-textarea
                  rows="3"
                  v-model="room.request"
                  :hide-details="true"
                  outlined
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <hr />
                <div class="text-left">
                  <v-btn
                    dark
                    small
                    :loading="loading"
                    color="primary"
                    @click="store"
                  >
                    Submit
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </div>
</template>

<script>
export default {
  props: ["reservation"],
  data: () => ({
    Model: "Reservation",
    snackbar: false,
    checkLoader: false,
    response: "",
    preloader: false,
    loading: false,
    show_password: false,
    show_password_confirm: false,
    roomTypes: [],
    types: ["Online", "Walking", "Travel Agency", "Complimentary"],
    rooms: [],
    sources: [
      "MakeMyTrip",
      "OYO Rooms",
      "Airbnb.co.in",
      "Expedia.co.in",
      "Trivago.in",
      "Yatra",
      "Cleartrip",
      "in.hotels.com",
      "Booking.com",
      "TripAdvisor.in"
    ],
    idCards: [],

    //check in calender
    check_in_menu: false,
    //check out calender
    check_out_menu: false,

    upload: {
      name: ""
    },

    member_numbers: [1, 2, 3, 4],

    isOnline: false,
    isAgent: false,
    fahath: "",

    room: {
      type: "",
      source: "",
      agent_name: "",
      room_type: "",
      room_id: "",
      check_in: null,
      check_out: null,
      discount: 0,
      advance_price: 0,
      payment_mode_id: 1,

      total_days: 0,
      sub_total: 0,
      after_discount: 0,
      sales_tax: 0,
      total_price: 0,
      remaining_price: 0,

      request: "",

      amount: 0,
      price: 0,

      company_id: 0
    },
    customer: {
      first_name: "",
      last_name: "",
      contact_no: "",
      email: "",
      id_card_type_id: "",
      id_card_no: "",
      car_no: "",
      no_of_adult: "",
      no_of_child: "",
      no_of_baby: "",
      address: "",
      company_id: ""
    },
    errors: []
  }),

  created() {
    // this.room.discount = 0;
    // this.room.advance_price = 0;
    // this.room.sales_tax = 0;
    this.preloader = false;
    this.get_id_cards();
    this.runAllFunctions();
  },

  methods: {
    runAllFunctions() {
      this.getDays();
      this.subTotal();
      this.afterDiscount();
      this.getAmountAfterSalesTax();
      this.getTotal();
      this.getRemainingAmount();
    },
    getDays() {
      // let ci = new Date(this.room.check_in); //reference
      let ci = new Date(this.reservation.check_in);
      let co = new Date(this.reservation.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      if (days > 0) {
        return (this.room.total_days = days);
      }
    },

    getAmountAfterSalesTax() {
      let amount = this.afterDiscount();
      let per = amount < 3000 ? 12 : 18;
      return (this.room.sales_tax = this.getPercentage(amount, per).toFixed(0));
    },

    afterDiscount() {
      this.room.after_discount = this.subTotal() - this.room.discount;
      return this.room.after_discount;
    },

    getTotal() {
      return (this.room.total_price =
        parseInt(this.getAmountAfterSalesTax()) +
        this.subTotal() -
        this.room.discount);
    },

    getRemainingAmount() {
      return (this.room.remaining_price =
        this.getTotal() - this.room.advance_price);
    },

    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },

    subTotal() {
      return (this.room.sub_total = this.reservation.price * this.getDays());
    },

    getType(val) {
      if (val == "Online") {
        this.isOnline = true;
        this.isAgent = false;
        return;
      }
      if (val == "Travel Agency") {
        this.isOnline = false;
        this.isAgent = true;
        return;
      }
      this.isOnline = false;
      this.isAgent = false;
    },

    get_id_cards() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`get_id_cards`, payload).then(({ data }) => {
        this.idCards = data;
      });
    },

    get_customer() {
      this.errors = [];
      let contact_no = this.customer.contact_no;
      if (contact_no == undefined) {
        alert("Enter contact number");
        this.checkLoader = false;
        return;
      }

      this.$axios.get(`get_customer/${contact_no}`).then(({ data }) => {
        if (!data.status) {
          this.checkLoader = false;

          this.customer = {};
          this.customer.contact_no = contact_no;
          return;
        }

        this.customer = {
          ...data.data,
          customer_id: data.data.id
        };
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    store() {
      if (this.reservation.isCalculate) {
        alert("Please calculate amount");
        return;
      }
      this.room.check_in = this.reservation.check_in;
      this.room.check_out = this.reservation.check_out;
      this.room.room_type = this.reservation.room_type;
      this.room.room_id = this.reservation.room_id;

      let payload = {
        ...this.room,
        company_id: this.$auth.user.company.id
      };
      console.log(payload);
      return;
      this.$axios
        .post("/booking_validate", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.store_customer();
          }
        })
        .catch(e => console.log(e));
    },
    store_customer() {
      let payload = {
        ...this.customer,
        company_id: this.$auth.user.company.id
      };

      this.$axios
        .post("/customer", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.store_booking(data.record.id);
          }
        })
        .catch(e => console.log(e));
    },
    store_booking(id) {
      let payload = {
        ...this.room,
        customer_id: id,
        company_id: this.$auth.user.company.id
      };
      this.$axios
        .post("/booking", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            location.reload();
          }
        })
        .catch(e => console.log(e));
    }
  }
};
</script>

<style scoped>
.v-text-field.v-text-field--enclosed .v-text-field__details {
  padding-top: 0px;
  margin-bottom: 8px;
  display: none;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 7px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
