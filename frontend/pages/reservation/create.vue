<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col cols="6"> </v-col>
    </v-row>

    <v-card elevation="0">
      <v-card-title primary-title> Room Information </v-card-title>
      <v-divider></v-divider>
      <v-container grid-list-xs>
        <v-row dense>
          <v-col md="6" sm="12" cols="12" dense>
            <label class="col-form-label"
              >Type <span class="text-danger">*</span></label
            >
            <v-select
              v-model="room.type"
              :items="types"
              dense
              outlined
              @change="getType(room.type)"
              :hide-details="!errors.type"
              :error="errors.type"
              :error-messages="errors && errors.type ? errors.type[0] : ''"
            ></v-select>
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
            <v-select
              v-model="room.source"
              :items="sources"
              dense
              outlined
              :hide-details="!errors.source"
              :error="errors.source"
              :error-messages="errors && errors.source ? errors.source[0] : ''"
            ></v-select>
          </v-col>

          <v-col md="6" sm="12" cols="12" dense>
            <label class="col-form-label"
              >Room Type <span class="text-danger">*</span></label
            >
            <v-select
              v-model="room.room_type"
              :items="roomTypes"
              item-text="name"
              item-value="id"
              dense
              outlined
              @change="get_room(room.room_type)"
              :hide-details="!errors.room_type"
              :error="errors.room_type"
              :error-messages="
                errors && errors.room_type ? errors.room_type[0] : ''
              "
            ></v-select>
          </v-col>

          <v-col md="6" sm="12" cols="12" dense>
            <label class="col-form-label">Room No </label>
            <v-select
              v-model="room.room_id"
              :items="rooms"
              item-text="room_no"
              item-value="id"
              dense
              outlined
              :hide-details="!errors.room_id"
              :error="errors.room_id"
              :error-messages="
                errors && errors.room_id ? errors.room_id[0] : ''
              "
            ></v-select>
          </v-col>

          <v-col cols="12" sm="6" md="6">
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
                  v-model="room.check_in"
                  readonly
                  v-bind="attrs"
                  :hide-details="true"
                  v-on="on"
                  dense
                  outlined
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="room.check_in"
                @input="check_in_menu = false"
                @change="getDays"
              ></v-date-picker>
            </v-menu>
          </v-col>

          <v-col cols="12" sm="6" md="6">
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
                  v-model="room.check_out"
                  readonly
                  v-bind="attrs"
                  :hide-details="true"
                  v-on="on"
                  dense
                  outlined
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="room.check_out"
                @input="check_out_menu = false"
                @change="getDays"
              ></v-date-picker>
            </v-menu>
          </v-col>

          <v-col md="6" cols="12" sm="12">
            <label class="col-form-label">Amount</label>
            <v-text-field
              dense
              :hide-details="true"
              outlined
              type="text"
              v-model="room.price"
            ></v-text-field>
          </v-col>

          <v-col md="12" cols="12" class="mt-3" sm="12">
            <h5>Total Days : {{ getDays() }}</h5>
            <h5>Price : {{ room.price }}</h5>
            <h5>Total Amount : {{ calAmount() }}</h5>
          </v-col>
        </v-row>
      </v-container>
    </v-card>

    <v-card elevation="0" class="mt-3">
      <v-card-title primary-title> Customer Information </v-card-title>
      <v-divider></v-divider>
      <v-container grid-list-xs>
        <v-row dense>
          <v-col md="4" cols="12" sm="12">
            <label class="col-form-label">Contact No</label>
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
          <v-col md="2" class="mt-10" cols="12" sm="12">
            <label class="col-form-label"></label>
            <v-btn
              color="primary"
              @click="get_customer()"
              :loading="checkLoader"
              >Check Customer</v-btn
            >
          </v-col>
          <v-col md="6" cols="12" sm="12">
            <label class="col-form-label">First Name</label>
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
          <v-col md="6" cols="12" sm="12">
            <label class="col-form-label">Last Name</label>
            <v-text-field
              dense
              :hide-details="true"
              outlined
              type="text"
              v-model="customer.last_name"
            ></v-text-field>
          </v-col>
          <v-col md="6" cols="12" sm="12">
            <label class="col-form-label">Email</label>
            <v-text-field
              dense
              :hide-details="true"
              outlined
              type="text"
              v-model="customer.email"
            ></v-text-field>
          </v-col>

          <v-col md="6" sm="12" cols="12" dense>
            <label class="col-form-label"
              >ID Card Type <span class="text-danger">*</span></label
            >
            <v-select
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
            ></v-select>
          </v-col>

          <v-col md="6" cols="12" sm="12">
            <label class="col-form-label">Selected ID Card Number</label>
            <v-text-field
              dense
              outlined
              :hide-details="true"
              type="text"
              v-model="customer.id_card_no"
            ></v-text-field>
          </v-col>
          <v-col md="6" cols="12" sm="12">
            <label class="col-form-label">Car Number</label>
            <v-text-field
              dense
              outlined
              :hide-details="true"
              type="text"
              v-model="customer.car_no"
            ></v-text-field>
          </v-col>
          <v-col md="1" sm="12" cols="12" dense>
            <label class="col-form-label">Adult </label>
            <v-select
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
            </v-select>
          </v-col>
          <v-col md="1" sm="12" cols="12" dense>
            <label class="col-form-label">Child </label>
            <v-select
              v-model="customer.no_of_child"
              :items="member_numbers"
              :hide-details="true"
              dense
              outlined
            >
            </v-select>
          </v-col>
          <v-col md="1" sm="12" cols="12" dense>
            <label class="col-form-label">Baby </label>
            <v-select
              v-model="customer.no_of_baby"
              :items="member_numbers"
              dense
              :hide-details="true"
              outlined
            >
            </v-select>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <label class="col-form-label">Address </label>
            <v-text-field
              v-model="customer.address"
              outlined
              :hide-details="true"
              textarea
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <label class="col-form-label">Customer Request </label>
            <v-text-field
              v-model="customer.request"
              :hide-details="true"
              outlined
              textarea
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <div class="text-right">
              <v-btn
                dark
                small
                :loading="loading"
                color="primary"
                @click="store()"
              >
                Submit
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </div>
</template>

<script>
export default {
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

    room: {
      amount: "",
      total_price: "",
      source: "",
      price: "",
      type: "",
      agent_name: "",
      source: "",
      room_type: "",
      room_id: "",
      check_in: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      check_out: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10)
    },
    customer: {},
    errors: []
  }),
  created() {
    this.preloader = false;
    this.get_room_types();
    this.get_id_cards();

    this.getDays();
  },
  methods: {
    getDays() {
      let ci = new Date(this.room.check_in);
      let co = new Date(this.room.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      if (days > 0) {
        return days;
      }
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
    get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
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

    get_room(val) {
      let room_type = this.roomTypes.find(e => e.id == val);
      this.room.price = room_type.price;
      this.$axios.get(`get_room/${val}`).then(({ data }) => {
        this.rooms = data;
      });
      this.calAmount();
    },
    get_customer() {
      this.checkLoader = true;
      let contact_no = this.customer.contact_no;
      console.log(contact_no);
      if (contact_no == undefined) {
        alert("Enter contact number");
        return;
      }

      this.$axios.get(`get_customer/${contact_no}`).then(({ data }) => {
        console.log(data);
        if (!data.status) {
          alert("No exist customer");
          this.customer = {};
          this.customer.contact_no = contact_no;
          return;
        }
        this.customer = {
          ...data.data
        };

        this.checkLoader = false;
      });
    },

    calAmount() {
      this.room.total_price = this.room.price * this.getDays();
      return this.room.total_price;
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    store() {
      let payload = {
        ...this.customer,
        ...this.room
      };

      this.errors = payload;
      this.$axios
        .post("/booking", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
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
</style>
