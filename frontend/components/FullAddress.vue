<template>
  <v-row>
    <v-col md="3" cols="12" sm="12">
      <v-autocomplete
        :items="countries"
        item-text="name"
        item-value="name"
        label="Country"
        v-model="location.country"
        outlined
        :hide-details="true"
        dense
        @change="getStates(location.country)"
      ></v-autocomplete>
    </v-col>
    <v-col md="3" cols="12" sm="12">
      <v-autocomplete
        :readonly="location.country == 'International'"
        :items="states"
        item-text="name"
        item-value="name"
        label="State"
        v-model="location.state"
        outlined
        :hide-details="true"
        dense
        @change="getCities"
      ></v-autocomplete>
    </v-col>
    <v-col md="3" cols="12" sm="12">
      <v-autocomplete
        :readonly="location.country == 'International'"
        :items="cities"
        label="City"
        v-model="location.city"
        outlined
        :hide-details="true"
        dense
        @change="exposeLocation"
      ></v-autocomplete>
    </v-col>
    <v-col md="3" cols="12" sm="12">
      <v-text-field
        :readonly="location.country == 'International'"
        label="Zip Code"
        v-model="location.zip_code"
        outlined
        :hide-details="true"
        dense
        @blur="exposeLocation"
      ></v-text-field>
    </v-col>
  </v-row>
</template>
<script>
export default {
  props: ["data", "headers"],
  data: () => ({
    countries: require("@/json/countries.json"),
    states: [],
    cities: [],
    location: {
      country: null,
      state: null,
      city: null,
      zip_code: null,
    },
  }),
  methods: {
    getStates(country) {
      if (country == "International") {
        this.exposeLocation();
      }
      // Find the country object from the countries array
      const countryObj = this.countries.find((e) => e.name === country);

      // Check if the country object exists
      if (countryObj) {
        // Set the states array from the found country object
        this.states = countryObj.states || [];
        this.location.state = null;
        this.location.city = null;
        this.location.zip_code = null;
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
    exposeLocation() {
      this.$emit("location", this.location);
    },
  },
};
</script>
