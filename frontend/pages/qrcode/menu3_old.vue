<template>
  <div>
    <v-row style="width: 100%; text-align: center; margin: 0px">
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-office-building-outline </v-icon
        ><br />Hotel</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-food-outline </v-icon
        ><br />Food Menu</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-washing-machine</v-icon
        ><br />Laundry</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2"> mdi mdi-vacuum</v-icon
        ><br />House Keeping</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2"> mdi mdi-dresser</v-icon
        ><br />Room Supplies</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-human-dolly</v-icon
        ><br />Porter Service</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-account-wrench</v-icon
        ><br />Room Problems</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-airplane-takeoff</v-icon
        ><br />Checkout</v-col
      >
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-card-account-phone</v-icon
        ><br />Phonebook</v-col
      >
    </v-row>
  </div>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    id: "",
  }),
  auth: false,
  mounted() {},
  created() {
    //this.id = this.$route.params.id;
  },
  methods: {},
};
</script>
