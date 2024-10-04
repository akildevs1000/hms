<template>
  <v-row no-gutters>
    <v-col md="2" cols="12">
      <ViewBox
        ref="ViewBox"
        :id="$route.params.id"
        :customer="booking.customer"
      />
      <div>
        <v-img
          @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
          class="zoom-on-hover"
          style="z-index: 1; width: 100%"
          :src="
            booking?.customer?.captured_photo ||
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
          "
        />
      </div>
      <v-row>
        <v-col cols="6">
          <div style="height: 25px" class="py-1">
            <v-img
              @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
              class="zoom-on-hover"
              style="width: 100%"
              :src="booking?.customer?.id_frontend_side || '/idf.png'"
            />
          </div>
        </v-col>
        <v-col cols="6">
          <div style="height: 25px" class="py-1">
            <v-img
              @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
              class="zoom-on-hover"
              style="width: 100%"
              :src="booking?.customer?.id_backend_side || '/idb.png'"
            />
          </div>
        </v-col>
      </v-row>
    </v-col>
    <v-col>
      <v-row no-gutters class="px-1 ma-1">
        <v-col cols="12">
          <v-row no-gutters>
            <v-col class="ma-1" cols="2">
              <v-select
                @change="submit"
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
            <v-col class="ma-1">
              <v-text-field
                @blur="submit"
                label="First Name *"
                dense
                outlined
                type="text"
                @change="submit"
                v-model="customer.first_name"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col class="ma-1">
              <v-text-field
                @blur="submit"
                label="Last Name"
                dense
                hide-details
                outlined
                type="text"
                @change="submit"
                v-model="customer.last_name"
              ></v-text-field>
            </v-col>
            <v-col class="ma-1" cols="4">
              <v-text-field
                @blur="submit"
                dense
                label="Email *"
                outlined
                type="email"
                @change="submit"
                v-model="customer.email"
                hide-details
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row no-gutters>
            <v-col class="ma-1">
              <v-text-field
                @blur="submit"
                dense
                label="Contact No *"
                outlined
                max="1111111111111"
                type="number"
                @change="submit"
                v-model="customer.contact_no"
                hide-details
                @keyup="mergeContact"
              ></v-text-field>
            </v-col>
            <v-col class="ma-1">
              <v-text-field
                @blur="submit"
                dense
                label="Whatsapp No"
                outlined
                max="1111111111111"
                type="number"
                @change="submit"
                v-model="customer.whatsapp"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col class="ma-1">
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
                  @input="
                    () => {
                      customer.dob_menu = false;
                      submit();
                    }
                  "
                ></v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
          <v-row no-gutters>
            <v-col class="ma-1">
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
            <v-col class="ma-1">
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
            <v-col class="ma-1">
              <v-autocomplete
                :items="cities"
                label="City"
                v-model="customer.city"
                outlined
                hide-details
                dense
                @blur="submit"
              ></v-autocomplete>
            </v-col>
            <v-col class="ma-1">
              <v-text-field
                label="Zip Code"
                @change="submit"
                v-model="customer.zip_code"
                outlined
                hide-details
                dense
                @blur="submit"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
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
        title: null,
        whatsapp: "",
        nationality: "-----",
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
        dob: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
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

      this.submit();
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

      this.submit();
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

    submit() {
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
