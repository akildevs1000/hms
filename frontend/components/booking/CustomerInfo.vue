<template>
  <span>
    <v-row>
      <v-col md="2" cols="12">
        <v-row no-gutters class="pa-2">
          <v-col class="text-right">
            <v-icon color="primary" small>mdi-eye</v-icon>
            <!-- <BookingIDPreview v-if="initialImage" :BookingId="1" /> -->
          </v-col>

          <v-col cols="12" class="mt-2">
            <v-img
              :src="
                (customer && customer.captured_photo) || '/no-profile-image.png'
              "
            ></v-img>
          </v-col>
          <v-col cols="6">
            <v-img
              :src="(customer && customer.captured_photo) || '/idf.png'"
              style="margin: 0 auto; width: 50px; height: 50px"
              contain
            ></v-img>
          </v-col>
          <v-col cols="6">
            <v-img
              :src="(customer && customer.captured_photo) || '/idb.png'"
              style="margin: 0 auto; width: 50px; height: 50px"
              contain
            ></v-img>
          </v-col>
        </v-row>
      </v-col>
      <v-col md="10" cols="12">
        <v-row v-if="isGroupBooking">
          <v-col md="4" cols="12" sm="12">
            <v-text-field
              label="Group Name *"
              dense
              outlined
              type="text"
              v-model="group_name"
              hide-details
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="4" dense>
            <v-autocomplete
              label="Business Source"
              v-model="customer.customer_type"
              :items="business_sources"
              dense
              item-text="name"
              item-value="name"
              outlined
              hide-details
            ></v-autocomplete>
          </v-col>
          <v-col md="8">
            <SourceType
              :isOverride="canOverride"
              :defaultSource="booking"
              :key="sourceCompKey"
              @sourceObject="handleSource"
            />
          </v-col>
          <v-col md="2" cols="12" sm="12">
            <v-select
              v-model="customer.title"
              :items="titleItems"
              label="Title *"
              dense
              item-text="name"
              item-value="name"
              hide-details
              outlined
            ></v-select>
          </v-col>
          <v-col md="3" cols="12" sm="12">
            <v-text-field
              label="First Name *"
              dense
              outlined
              type="text"
              v-model="customer.first_name"
              hide-details
            ></v-text-field>
          </v-col>
          <v-col md="3" cols="12" sm="12">
            <v-text-field
              label="Last Name"
              dense
              hide-details
              outlined
              type="text"
              v-model="customer.last_name"
            ></v-text-field>
          </v-col>
          <v-col md="4" cols="12" sm="12">
            <v-text-field
              dense
              label="Email *"
              outlined
              type="email"
              v-model="customer.email"
              hide-details
            ></v-text-field>
          </v-col>
          <v-col md="4" cols="12" sm="12">
            <v-text-field
              dense
              label="Contact No *"
              outlined
              max="1111111111111"
              type="number"
              v-model="customer.contact_no"
              hide-details
              @keyup="mergeContact"
            ></v-text-field>
          </v-col>
          <v-col md="4" cols="12" sm="12">
            <v-text-field
              dense
              label="Whatsapp No"
              outlined
              max="1111111111111"
              type="number"
              v-model="customer.whatsapp"
              hide-details
            ></v-text-field>
          </v-col>
          <v-col md="4" cols="12" sm="12">
            <v-menu
              v-model="customer.dob_menu"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="customer.dob"
                  readonly
                  label="DOB"
                  v-on="on"
                  v-bind="attrs"
                  hide-details
                  dense
                  outlined
                ></v-text-field>
              </template>
              <v-date-picker
                no-title
                v-model="customer.dob"
                @input="customer.dob_menu = false"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col md="4" cols="12" sm="12">
            <v-autocomplete
              v-model="customer.nationality"
              :items="countries"
              label="Nationality"
              item-text="name"
              item-value="name"
              dense
              outlined
              hide-details
            ></v-autocomplete>
          </v-col>
          <v-col md="4">
            <v-select
              label="Purpose"
              v-model="booking.purpose"
              :items="purposes"
              dense
              hide-details
              outlined
            ></v-select>
          </v-col>
          <v-col md="4" cols="12" sm="12">
            <v-text-field
              dense
              label="Car Number"
              outlined
              hide-details
              type="text"
              v-model="customer.car_no"
            ></v-text-field>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="3" cols="12" sm="12">
        <v-autocomplete
          :items="countries"
          item-text="name"
          item-value="name"
          label="Country"
          v-model="customer.country"
          outlined
          hide-details
          dense
          @change="getStates(customer.country)"
        ></v-autocomplete>
      </v-col>
      <v-col md="3" cols="12" sm="12">
        <v-autocomplete
          :items="states"
          item-text="name"
          item-value="name"
          label="State"
          v-model="customer.state"
          outlined
          hide-details
          dense
          @change="getCities"
        ></v-autocomplete>
      </v-col>
      <v-col md="3" cols="12" sm="12">
        <v-autocomplete
          :items="cities"
          label="City"
          v-model="customer.city"
          outlined
          hide-details
          dense
        ></v-autocomplete>
      </v-col>
      <v-col md="3" cols="12" sm="12">
        <v-text-field
          label="Zip Code"
          v-model="customer.zip_code"
          outlined
          hide-details
          dense
        ></v-text-field>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="12">
        <v-textarea
          rows="3"
          label="Customer Request"
          v-model="booking.request"
          hide-details
          outlined
        ></v-textarea>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" class="text-right">
        <v-btn small @click="nextTab" color="primary">Next</v-btn>
      </v-col>
    </v-row>
  </span>
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["defaultCustomer"],
  props: {
    defaultCustomer: {
      type: Object,
    },
    isGroupBooking: {
      type: Boolean,
      default: () => false,
    },
    initialImage: {
      type: Boolean,
      default: () => true,
    },
  },
  data() {
    return {
      countries: require("@/json/countries.json"),
      states: [],
      cities: [],
      dialog: false,
      loading: false,

      purposes: [
        "Tour",
        "Business",
        "Hospital",
        "Holiday",
        "Party/Functions",
        "Friend Visit",
        "Marriage",
      ],
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,

      imgView: false,

      isDiff: false,
      sourceCompKey: 1,
      group_name: null,
      booking: {
        purpose: null,
        request: null,
        type: null,
        reference_no: null,
        paid_by: null,
      },
      countryList: [],
      titleItems: [
        { id: 1, name: "Mr" },
        { id: 2, name: "Mrs" },
        { id: 3, name: "Miss" },
        { id: 4, name: "Ms" },
        { id: 5, name: "Dr" },
      ],
      customer: {
        customer_type: null,
        title: "Mr",
        whatsapp: "",
        nationality: "",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        car_no: "",
        no_of_adult: 0,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        country: null,
        state: null,
        city: null,
        zip_code: null,
      },
      business_sources: [],
      canOverride: false,
    };
  },
  async created() {
    this.preloader = false;

    if (this.defaultCustomer && this.defaultCustomer.id) {
      this.canOverride = true;
      this.customer = this.defaultCustomer;
      this.getStates(this.customer.country);
      this.getCities(this.customer.state);

      if (this.customer.latest_booking) {
        
        let latest_booking = this.customer.latest_booking;

        this.booking = {
          type: latest_booking.type,
          source: latest_booking.source,
          purpose: latest_booking.purpose,
          request: latest_booking.request,
          reference_no: latest_booking.reference_no,
          paid_by: latest_booking.paid_by,
        };
      }
      this.sourceCompKey += 1;
    }
    await this.get_business_sources();
  },
  methods: {
    getStates(country) {
      // Find the country object from the countries array
      const countryObj = this.countries.find((e) => e.name === country);

      // Check if the country object exists
      if (countryObj) {
        // Set the states array from the found country object
        this.states = countryObj.states || [];
      } else {
        // If country not found, clear the states array and handle error
        this.states = [];
        console.warn(`Country with slug "${country}" not found.`);
      }
    },

    getCities(state) {
      // Find the state object from the states array
      const stateObj = this.states.find((e) => e.name === state);

      // Check if the state object exists
      if (stateObj) {
        // Set the cities array from the found state object
        this.cities = stateObj.cities || [];
      } else {
        // If state not found, clear the cities array and handle error
        this.cities = [];
        console.warn(`State "${state}" not found.`);
      }
    },
    handleSource(e) {
      this.booking = e;
    },
    async get_business_sources() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      let { data } = await this.$axios.get("business-source-list", config);
      this.business_sources = data;
    },
    nextTab() {
      if (!this.customer.customer_type) {
        this.$swal("Warning", "Select Business Source", "error");
        return;
      }
      if (!this.booking.type) {
        this.$swal("Warning", "Select Source Type", "error");
        return;
      }

      if (!this.customer.title) {
        this.$swal("Warning", "Customer title is required", "error");
        return;
      }

      if (!this.customer.first_name) {
        this.$swal("Warning", "Customer first name is required", "error");
        return;
      }

      if (!this.customer.last_name) {
        this.$swal("Warning", "Customer last name is required", "error");
        return;
      }

      if (!this.customer.contact_no) {
        this.$swal("Warning", "Customer Contact no is required", "error");
        return;
      }

      if (!this.customer.nationality) {
        this.$swal("Warning", "Customer Nationality is required", "error");
        return;
      }

      let payload = {
        customer: this.customer,
        booking: { ...this.booking, group_name: this.group_name },
      };

      this.$emit(`selectedCustomer`, payload);
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      }
    },
  },
};
</script>
