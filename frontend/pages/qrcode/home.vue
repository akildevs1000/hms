<template>
  <div v-if="isPageValid()">
    <v-row style="width: 100%; text-align: center; margin: 0px">
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-office-building-outline </v-icon
        ><br />Hotel</v-col
      >
      <v-col cols="6" class="boxborder" @click="openPage('food_menu')">
        <v-icon size="100" color="blue-grey darken-2">
          mdi mdi-food-outline
        </v-icon>
        <br />
        Food Menu
      </v-col>

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
      <v-col cols="6" class="boxborder"
        ><v-icon size="100" color="blue-grey darken-2">
          mdi mdi-view-gallery</v-icon
        ><br />Photo Gallery</v-col
      >
    </v-row>
  </div>
  <div v-else>UnAuthorised Access</div>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    id: "",
    pageValid: false,
  }),
  auth: false,
  mounted() {
    this.pageValid = localStorage.getItem("hotelQRCodeOTPverified");
  },
  created() {
    // console.log(
    //   "hotelQrcodeRoomNumber",
    //   this.$store.state.hotelQrcodeRoomNumber
    // );
    // if (this.$store.state.hotelQrcodeRoomNumber) {
    // } else {
    //   this.pageValid = false;
    // }
  },
  methods: {
    isPageValid() {
      return this.pageValid;
      // if (
      //   this.$store.state.hotelQRCodeOTPverified ||
      // ) {
      //   return true;
      // } else {
      //   this.pageValid = false;
      //   return false;
      // }
    },
    openPage(name) {
      this.$router.push("/qrcode/" + name);
    },
  },
};
</script>
