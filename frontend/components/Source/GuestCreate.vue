<template>
  <v-dialog v-model="NewCustomerDialog" max-width="750px">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on" small>
        <v-icon small color="black">mdi-plus</v-icon> Guest
      </span>
    </template>

    <v-card>
      <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
        <span>Create Guest </span>
        <v-spacer></v-spacer>
        <SearchCustomer @foundCustomer="handleFoundCustomer" />
        <AssetsButtonClose @close="close" />
      </v-toolbar>

      <!-- <v-alert class="rounded-md" color="grey lighten-3" dense flat>
        <v-row no-gutter>
          <v-col>
            <span>Create Guest</span>
          </v-col>
          <v-col class="text-right">
            <SearchCustomer @foundCustomer="handleFoundCustomer" />
            <AssetsButtonClose @close="close" />
          </v-col>
        </v-row>
      </v-alert> -->
      <v-card-text>
        <v-container>
          <span>
            <v-row>
              <v-col md="3" cols="12">
                <v-row no-gutters class="pa-2">
                  <v-col cols="12" class="text-center">
                    <v-avatar tile size="120">
                      <v-card>
                        <img
                          class="zoom-on-hover"
                          style="z-index: 1; width: 100%"
                          :src="
                            customer?.captured_photo ||
                            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                          "
                        />
                        <!-- <div class="text-right pa-2">
                              <v-icon
                                color="white"
                               
                                >mdi-eye</v-icon
                              >
                            </div> -->
                      </v-card>
                    </v-avatar>
                  </v-col>
                  <v-col cols="6" class="text-center">
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
              <v-col md="9" cols="12">
                <v-row>
                  <v-col md="3" cols="12" sm="12">
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
                  <v-col md="9" cols="12" sm="12">
                    <v-row>
                      <v-col>
                        <v-text-field
                          label="First Name *"
                          dense
                          outlined
                          type="text"
                          v-model="customer.first_name"
                          hide-details
                        ></v-text-field>
                      </v-col>
                      <v-col>
                        <v-text-field
                          label="Last Name"
                          dense
                          hide-details
                          outlined
                          type="text"
                          v-model="customer.last_name"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      dense
                      label="Email *"
                      outlined
                      type="email"
                      v-model="customer.email"
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-menu
                      v-model="dob_menu"
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
                        @input="dob_menu = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="6">
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
                  <v-col cols="6">
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
                </v-row>
              </v-col>
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
              <v-col cols="12" class="text-right">
                <AssetsButtonCancel @close="close" />
                &nbsp;&nbsp;
                <AssetsButtonSubmit @click="submit" />
              </v-col>
            </v-row>
          </span>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
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
      NewCustomerDialog: false,
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
      dob_menu: false,

      customer: {
        customer_type: null,
        title: "Mr",
        whatsapp: "",
        nationality: "",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        image: "",
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
    handleFoundCustomer(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };
    },
    close() {
      this.customer = {
        customer_type: null,
        title: "Mr",
        whatsapp: "",
        nationality: "",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        image: "",
        dob: null,
        country: null,
        state: null,
        city: null,
        zip_code: null,
      };

      this.NewCustomerDialog = false;

      this.$emit("close-dialog");
    },
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
    },
    submit() {
      this.customer.company_id = this.$auth.user.company.id;

      this.$axios
        .post("/store_customer", this.customer)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.$swal(
              "Success!",
              "successfully added customer",
              "success"
            ).then((e) => {
              this.errors = [];
              this.close();
            });
          }
        })
        .catch((e) => console.log(e));
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      }
    },
  },
};
</script>
